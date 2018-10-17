<?php
include( '../init.inc.php' );

$mediasrv2 = 'https://ba14ns21403-sec1.fhnw.ch/mediasrv';
$mediacollection = 'PeterVonArx';
$jwtprefix = 'mediaserver:';
$jwtkey = 'pP7ZMIAWY2zHgP3Nrge9';
$thumbparams = 'resize/sharpen0x3/size150x150'; //   /sharpen0x3

if( !$session->isLoggedIn()) {
  die( "not logged in" );
}
if( !$session->isAdmin()) {
  die( 'admin rights required' );
}



function getVal( $name ) {
  return isset( $_REQUEST[$name] ) ? $_REQUEST[$name] : null;
}

function isCol( $col ) {
  static $cols = [
    'masterid',
    'collectinid',
    'collectionname',
    'signature',
    'copyright',
    'license',
    'access',
    'reference',
    'embargo',
    'endoflife',
  ];

  return array_search( $col, $cols ) !== false;
}

$draw = intval( getVal( 'draw' ));
$start = intval( getVal( 'start' ));
$length = intval( getVal( 'length' ));
$search = getVal( 'search' );
$order = getVal( 'order' ); if( $order == null ) $order = array();
$columns = getVal( 'columns' ); if( $columns == null ) $columns = array();

$data = [];
$sqls = [];
$sql = "SELECT COUNT(*) FROM mediaserver.master";
$sqls[] = $sql;
$maxRecordCount = intval( $db->getOne( $sql ));
$recordCount = $maxRecordCount;

$srchsOR = [];
$srchsAND = [];
if( $search ) {
  $sval = trim( $search['value']);
  if( strlen( $sval )) {
    $sval = "%{$sval}%";
    foreach( $columns as $key=>$col ) {
      if( $col['searchable'] == 'true' ) {
        $srchsOR[] = "`{$col['data']}` LIKE ".$db->qstr( $sval );
      }
    }
  }
}
if( $columns ) foreach( $columns as $colid=>$col ) {
  if( !isset( $col['data'] )) continue;
  if( $col['searchable'] ) {
    if( isset( $col['search']['value'] ) && strlen( trim( $col['search']['value'] ))) {
      $srchsAND[] = "`{$col['data']}` = ".$db->qstr( $col['search']['value'] );
    }
  }
}
$sql = "SELECT COUNT(*) FROM mediaserver.fullmeta WHERE 1";
if( count( $srchsOR )) $sql .= ' AND ('.implode( ' OR ', $srchsOR ).')';
if( count( $srchsAND )) $sql .= ' AND ('.implode( ' AND ', $srchsAND ).')';
$sqls[] = $sql;
$recordCount = intval( $db->getOne( $sql ));

$sql = "SELECT masterid
  , `type`
  , collectionid
  , collectionname
  , signature
  , copyright
  , license
  , access
  , reference
  , embargo
  , endoflife
  FROM mediaserver.fullmeta WHERE 1";
if( count( $srchsOR )) $sql .= ' AND ('.implode( ' OR ', $srchsOR ).')';
if( count( $srchsAND )) $sql .= ' AND ('.implode( ' AND ', $srchsAND ).')';
$order_arr = [];
foreach( $order as $key=>$val ) {
  if( isCol( $columns[$val['column']]['data'] ) && $val['column'] < count($columns)) $order_arr[] = "`{$columns[$val['column']]['data']}` ".($val['dir']=='asc'? 'ASC':'DESC' );
}
if( count( $order_arr )) $sql .= ' ORDER BY '.implode( ', ', $order_arr );
if( $length > 0 ) $sql .= " LIMIT {$start}, {$length}";
$sqls[] = $sql;
$rs = $db->Execute( $sql );
//$recordCount = $rs->recordCount();
foreach( $rs as $row ) {
  $row['DT_RowId'] = $row['masterid'];
  $collection = $row['collectionname'];
  $signature = $row['signature'];
  $thumbsignature = $signature;
  if( $row['type'] == 'video' ) {
    $thumbsignature .= '$$timeshot$$3';
  }
  $payload = [
          'iss' => 'DIGMA Mediaserver',
          'sub' => strtolower( "{$jwtprefix}{$collection}/{$thumbsignature}/{$thumbparams}"),
          'exp' => time() + 4 * 3600,
        ];
  $token = jwt_encode($payload, $jwtkey);
  $row['thumb'] = "{$mediasrv2}/{$collection}/{$thumbsignature}/{$thumbparams}?token={$token}";
  $payload2 = [
          'iss' => 'DIGMA Mediaserver',
          'sub' => strtolower( "{$jwtprefix}{$row['collectionname']}/{$row['signature']}/iframe"),
          'exp' => time() + 4 * 3600,
        ];
  $token2 = jwt_encode($payload2, $jwtkey);
  $row['fullscreen'] = "{$mediasrv2}/{$row['collectionname']}/{$row['signature']}/iframe?token={$token2}";

  $data[] = $row;

}
$rs->Close();

$result = [
  'draw'=>$draw,
  'recordsTotal'=>$maxRecordCount,
  'recordsFiltered'=>$recordCount,
  'data'=>$data,
  'sql'=>$sqls,
];

echo json_encode( $result );
