<?php
namespace Mediathek;

header( 'Content-type: application/json' );

include( 'init.inc.php' );

if( !$session->inGroup( 'global/admin' )) {
    echo json_encode( array( 'status'=>'not logged in' ));
    exit;
}

$draw = @intval( $_REQUEST['draw'] );
$start = @intval( $_REQUEST['start'] );
$length = @intval( $_REQUEST['length'] );
$search = array_key_exists( 'search', $_REQUEST ) ? $_REQUEST['search'] : array();
$order = array_key_exists( 'order', $_REQUEST ) ? $_REQUEST['order'] : array( array( 'column'=>0, 'dir'=>'desc' ));
$columns = array_key_exists( 'columns', $_REQUEST ) ? $_REQUEST['columns'] : array();

$result = array();
$result['recordsTotal'] = intval( $db->GetOne( 'SELECT COUNT(*) FROM gate_event' ));
$result['draw'] = $draw;
$result['data'] = array();

$sqlsort = '';
foreach( $order as $o ) {
    if( $sqlsort != '' ) $sqlsort .= ', ';
    $dir = 'ASC';
    if( $o['dir'] == 'desc' ) $dir = 'DESC';
    switch( intval( $o['column'] )) {
      case 0:
        $sqlsort .= "gatetime {$dir}";
        break;
      case 1:
        $sqlsort .= "gate {$dir}";
        break;
      case 2:
        $sqlsort .= "uid {$dir}";
        break;
      case 3:
        $sqlsort .= "afi {$dir}";
        break;
      case 4:
        $sqlsort .= "status {$dir}";
        break;
      case 5:
        $sqlsort .= "itemid {$dir}";
        break;
      case 6:
        $sqlsort .= "country {$dir}, isil {$dir}";
        break;
    }
}

$sqlquery = '';
if( array_key_exists( 'value', $search )) {
  $val = $search['value'];
  $sqlquery .= ' (gatetime LIKE '.$db->qstr( $val.'%' )
  .' OR gate LIKE '.$db->qstr( $val.'%' )
  .' OR uid LIKE '.$db->qstr( $val.'%' )
  .' OR itemid LIKE '.$db->qstr( $val.'%' )
  .' OR CONCAT(country, "-",isil) LIKE '.$db->qstr( $val.'%' )
  //      .' OR status = '.intval($val)
  .')';
}

$sql = "SELECT COUNT(*) FROM gate_event";
if( strlen( $sqlquery )) $sql .= ' WHERE '.$sqlquery;
$result['recordsFiltered'] = intval( $db->GetOne( $sql ));

$sql = "SELECT * FROM gate_event";
if( strlen( $sqlquery )) $sql .= ' WHERE '.$sqlquery;
if( strlen( $sqlsort )) $sql .= ' ORDER BY '.$sqlsort;

$result['sql'] = $sql;

$rs = $db->SelectLimit( $sql, $length, $start );
//$result['queryRecordCount'] = $rs->recordCount();
foreach( $rs as $row ) {
/*
  $result['data'][] = array(
      'time'=>$row['gatetime']
      , 'gate'=>$row['gate']
      , 'uid'=>$row['uid']
      , 'afi'=>$row['afi']
      , 'status'=>$row['status']
      , 'item'=>$row['itemid']
      , 'isil'=>$row['country'].'-'.$row['isil']
  );
*/
  $status = $row['status'];
  $statusstring = $status;
  if( $status == 0) $statusstring .= ' - Ok';
  if( $status & (1 << 0)) $statusstring .= ' - CRC Error';
  if( $status & (1 << 1)) $statusstring .= ' - Falscher Barcode';
  if( $status & (1 << 2)) $statusstring .= ' - Unbekanntes Buch';
  if( $status & (1 << 3)) $statusstring .= ' - Nicht zur Ausleihe bestimmt';
  if( $status & (1 << 4)) $statusstring .= ' - Unbekannte UID';
  $statusstring = trim( $statusstring );

  $result['data'][] = array(
      $row['gatetime']
      , $row['gate']
      , $row['uid']
      , $row['afi']
      , $statusstring
      , $row['itemid']
      , $row['country'].'-'.$row['isil']
  );
}
$rs->Close();

echo json_encode( $result );

exit;


$page = @intval( $_REQUEST['page'] );
$perPage = @intval( $_REQUEST['perPage'] );
$offset = @intval( $_REQUEST['offset'] );
$sorts = array_key_exists( 'sorts', $_REQUEST ) ? $_REQUEST['sorts'] : array( 'time' => -1 );
$queries = array_key_exists( 'queries', $_REQUEST ) ? $_REQUEST['queries'] : array();


$result = array( 'records'=>array());

$sqlquery = '';
foreach( $queries as $key=>$val ) {
  if( $sqlquery != '' ) $sqlquery .= ', ';
  switch( $key ) {
    case 'search':
      $sqlquery .= ' (gatetime LIKE '.$db->qstr( $val.'%' )
      .' OR gate LIKE '.$db->qstr( $val.'%' )
      .' OR uid LIKE '.$db->qstr( $val.'%' )
      .' OR itemid LIKE '.$db->qstr( $val.'%' )
      .' OR CONCAT(country, "-",isil) LIKE '.$db->qstr( $val.'%' )
//      .' OR status = '.intval($val)
      .')';
      break;
  }
}

$sqlsort = '';
foreach( $sorts as $key=>$val ) {
    if( $sqlsort != '' ) $sqlsort .= ', ';
    switch( $key ) {
      case 'time':
        $sqlsort .= 'gatetime '.($val==1 ? 'ASC':'DESC');
        break;
      case 'gate':
      case 'uid':
      case 'afi':
      case 'status':
        $sqlsort .= $key.' '.($val==1 ? 'ASC':'DESC');
        break;
      case 'isil':
        $sqlsort .= 'country '.($val==1 ? 'ASC':'DESC').', isil '.($val==1 ? 'ASC':'DESC');
        break;
    }
}

$sql = "SELECT * FROM gate_event WHERE 1";
if( strlen( $sqlquery )) $sql .= ' AND '.$sqlquery;
if( strlen( $sqlsort )) $sql .= ' ORDER BY '.$sqlsort;

$rs = $db->PageExecute( $sql, $perPage, $page );
$result['queryRecordCount'] = $rs->recordCount();
$result['totalRecordCount'] = $rs->maxRecordCount();
foreach( $rs as $row ) {
  $result['records'][] = array(
      'time'=>$row['gatetime']
      , 'gate'=>$row['gate']
      , 'uid'=>$row['uid']
      , 'afi'=>$row['afi']
      , 'status'=>$row['status']
      , 'item'=>$row['itemid']
      , 'isil'=>$row['country'].'-'.$row['isil']
  );
}
$rs->Close();

echo json_encode( $result );
?>
