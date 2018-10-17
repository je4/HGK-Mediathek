<?php
include( '../init.inc.php' );

$config['mediasrv2'] = 'https://ba14ns21403-sec1.fhnw.ch/mediasrv';
$config['mediacollection'] = 'PeterVonArx';
$config['jwtprefix'] = 'mediaserver:';
$config['jwtkey'] = 'pP7ZMIAWY2zHgP3Nrge9';
$config['thumbparams'] = 'resize/sharpen0x3/size150x150'; //   /sharpen0x3

if( !$session->isLoggedIn()) {
  die( "not logged in" );
}

function getVal( $name ) {
  return isset( $_REQUEST[$name] ) ? $_REQUEST[$name] : null;
}

function isCol( $col ) {
  static $cols = [
    'reference',
    'copyright',
    'license',
    'embargo',
    'endoflife',
    'access'
  ];

  return array_search( $col, $cols ) !== false;
}

$action = getVal( 'action' );
$data = getVal( 'data' );

function edit() {
  global $db, $data, $config;

  $result = ['data'=>[], 'sql'=>[]];

  foreach( $data as $key=>$val ) {
    $masterid = intval( $key );

    $sql = "SELECT COUNT(*) FROM mediaserver.metadata WHERE masterid=".intval( $key );
    $result['sql'][] = $sql;
    $found = intval( $db->GetOne( $sql ));
    if( !$found ) {
      $sql = "INSERT INTO mediaserver.metadata(masterid) VALUES( {$key} )";
      $result['sql'][] = $sql;
      $db->Execute( $sql );
    }

    $vals = [];
    foreach( $val as $fld=>$v ) {
      if( !isCol( $fld )) {
        $result['error'] = 'invalid field '.$fld;
        return $result;
      }
      $vals[] = "`{$fld}`=".$db->qstr( strlen($v) ? $v : null );
    }
    $sql = "UPDATE mediaserver.metadata SET";
    $sql .= ' '.implode( ', ', $vals );
    $sql .= ' WHERE masterid='.$masterid;
    $result['sql'][] = $sql;
    $db->Execute( $sql );
    $sql = "SELECT masterid
      , collectionid
      , collectionname
      , signature
      , copyright
      , license
      , access
      , reference
      , embargo
      , endoflife
      FROM mediaserver.fullmeta WHERE masterid=".$masterid;
    $row = $db->GetRow( $sql );
    $row['DT_RowId'] = $row['masterid'];
    $result['data'][] = $row;
  }
  return $result;
}

function create() {
  global $db, $data, $config;

  $result = ['data'=>[], 'sql'=>[]];
  $result['error'] = 'create not supported';
  return $result;
}

function remove() {
  global $db, $data, $config;

  $result = ['data'=>[], 'sql'=>[]];

  $result['error'] = 'remove not supported';
  return $result;
}

$result = null;
switch( $action ) {
  case 'edit':
    $result = edit();
  break;
  case 'remove':
    $result = remove();
  break;
  case 'create':
    $result = create();
  break;
}

echo json_encode( $result );
