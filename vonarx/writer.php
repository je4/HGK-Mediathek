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

$action = getVal( 'action' );
$data = getVal( 'data' );

function edit() {
  global $db, $data, $config;

  $result = ['data'=>[], 'sql'=>[]];

  foreach( $data as $key=>$val ) {
    $sql = "UPDATE source_vonarx_box1 SET";
    $vals = [];
    foreach( $val as $fld=>$v ) {
      $vals[] = "`{$fld}`=".$db->qstr( $v );
    }
    $sql .= ' '.implode( ', ', $vals );
    $sql .= ' WHERE ObjectNr='.$db->qstr( $key );
    $result['sql'][] = $sql;
    $db->Execute( $sql );
    $sql = "SELECT * FROM source_vonarx_box1 WHERE ObjectNr=".$db->qstr( $key );
    $row = $db->GetRow( $sql );
    $row['DT_RowId'] = $row['ObjectNr'];

    $payload = [
            'iss' => 'DIGMA Mediaserver',
            'sub' => strtolower( "{$config['jwtprefix']}{$config['mediacollection']}/{$row['ObjectNr']}/{$config['thumbparams']}"),
            'exp' => time() + 4 * 3600,
          ];
    $token = jwt_encode($payload, $config['jwtkey']);
    $row['Thumb'] = "{$config['mediasrv2']}/{$config['mediacollection']}/{$row['ObjectNr']}/{$config['thumbparams']}?token={$token}";
    $payload2 = [
            'iss' => 'DIGMA Mediaserver',
            'sub' => strtolower( "{$config['jwtprefix']}{$config['mediacollection']}/{$row['ObjectNr']}/iframe"),
            'exp' => time() + 4 * 3600,
          ];
    $token2 = jwt_encode($payload2, $config['jwtkey']);
    $row['Fullscreen'] = "{$config['mediasrv2']}/{$config['mediacollection']}/{$row['ObjectNr']}/iframe?token={$token2}";
    $result['data'][] = $row;
  }
  return $result;
}

function create() {
  global $db, $data, $config;

  $result = ['data'=>[], 'sql'=>[]];

  foreach( $data as $key=>$val ) {

    $flds = [];
    foreach( $val as $fld=>$v ) {
      $flds[] = "`$fld`";
    }
    $vals = [];
    foreach( $val as $fld=>$v ) {
      $vals[] = $db->qstr( $v );
    }
    $sql = "INSERT INTO source_vonarx_box1(".implode( ', ', $flds).') VALUES ( '.implode( ', ', $vals ). ' )';;
    $result['sql'][] = $sql;
    $db->Execute( $sql );
    $sql = "SELECT * FROM source_vonarx_box1 WHERE ObjectNr=".$db->qstr( $val['ObjectNr'] );
    $row = $db->GetRow( $sql );
    $row['DT_RowId'] = $row['ObjectNr'];

    $payload = [
            'iss' => 'DIGMA Mediaserver',
            'sub' => strtolower( "{$config['jwtprefix']}{$config['mediacollection']}/{$row['ObjectNr']}/{$config['thumbparams']}"),
            'exp' => time() + 4 * 3600,
          ];
    $token = jwt_encode($payload, $config['jwtkey']);
    $row['Thumb'] = "{$config['mediasrv2']}/{$config['mediacollection']}/{$row['ObjectNr']}/{$config['thumbparams']}?token={$token}";
    $payload2 = [
            'iss' => 'DIGMA Mediaserver',
            'sub' => strtolower( "{$config['jwtprefix']}{$config['mediacollection']}/{$row['ObjectNr']}/iframe"),
            'exp' => time() + 4 * 3600,
          ];
    $token2 = jwt_encode($payload2, $config['jwtkey']);
    $row['Fullscreen'] = "{$config['mediasrv2']}/{$config['mediacollection']}/{$row['ObjectNr']}/iframe?token={$token2}";
    $result['data'][] = $row;
  }
  return $result;
}

function remove() {
  global $db, $data, $config;

  $result = ['data'=>[], 'sql'=>[]];

  foreach( $data as $key=>$val ) {
    $sql = "DELETE FROM  source_vonarx_box1";
    $sql .= ' WHERE ObjectNr='.$db->qstr( $key );
    $result['sql'][] = $sql;
    $db->Execute( $sql );
  }
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
