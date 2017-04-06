<?php
namespace Mediathek;

header( 'Content-type: application/json' );

include( 'init.inc.php' );

if( !$session->inGroup( 'global/admin' )) {
    echo json_encode( array( 'status'=>'not logged in' ));
    exit;
}

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
