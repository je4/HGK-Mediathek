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

function getVal( $name ) {
  return isset( $_REQUEST[$name] ) ? $_REQUEST[$name] : null;
}

function isCol( $col ) {
  static $cols = [
    'Thumb',
    'Fullscreen',
    'Box',
    'ObjectNr',
    'Title',
    'Object',
    'Description',
    'Date',
    'Client',
    'Media',
    'Size',
    'Author',
    'Print',
    'Copyright',
    'Location',
    'Digital',
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
$sql = "SELECT COUNT(*) FROM source_vonarx_box1";
$sqls[] = $sql;
$maxRecordCount = intval( $db->getOne( $sql ));
$recordCount = $maxRecordCount;
$sql = "SELECT * FROM source_vonarx_box1";
$srchs = [];
if( $search ) {
  $sval = trim( $search['value']);
  if( strlen( $sval )) {
    $sval = "%{$sval}%";
    foreach( $columns as $key=>$val ) {
      if( $val['searchable'] == 'true' ) {
        $srchs[] = "`{$val['data']}` LIKE ".$db->qstr( $sval );
      }
    }
    $sql .= ' WHERE '.implode( ' OR ', $srchs );
  }
}
if( count( $srchs )) {
  $sql = "SELECT COUNT(*) FROM source_vonarx_box1 WHERE ".implode( ' OR ', $srchs );
  $sqls[] = $sql;
  $recordCount = intval( $db->getOne( $sql ));
}

$sql = "SELECT * FROM source_vonarx_box1";
if( count( $srchs )) $sql .= " WHERE ".implode( ' OR ', $srchs );
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
  $row['DT_RowId'] = $row['ObjectNr'];

  $payload = [
          'iss' => 'DIGMA Mediaserver',
          'sub' => strtolower( "{$jwtprefix}{$mediacollection}/{$row['ObjectNr']}/{$thumbparams}"),
          'exp' => time() + 4 * 3600,
        ];
  $token = jwt_encode($payload, $jwtkey);
  $row['Thumb'] = "{$mediasrv2}/{$mediacollection}/{$row['ObjectNr']}/{$thumbparams}?token={$token}";
  $payload2 = [
          'iss' => 'DIGMA Mediaserver',
          'sub' => strtolower( "{$jwtprefix}{$mediacollection}/{$row['ObjectNr']}/iframe"),
          'exp' => time() + 4 * 3600,
        ];
  $token2 = jwt_encode($payload2, $jwtkey);
  $row['Fullscreen'] = "{$mediasrv2}/{$mediacollection}/{$row['ObjectNr']}/iframe?token={$token2}";
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
