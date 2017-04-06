<?php
namespace Mediathek;

header( 'Content-type: application/json' );

include( 'init.inc.php' );

if( !$session->isLoggedIn()) {
    echo json_encode( array( 'status'=>'not logged in' ));
    exit;
}

$type = $_REQUEST['type'];
$_gate = array_key_exists( 'gate', $_REQUEST ) ? $_REQUEST['gate'] : null;

$result = array( array(
  'Counter',
  array( 'role'=>'annotation' ),
) );

if( $_gate == null ) $result = array( array(
  'Counter',
  'North #1',
  'North #2',
  'North #3',
  'North #4',
  'South #1',
  'South #2',
  'South #3',
  'South #4',
  array( 'role'=>'annotation' ),
) );
elseif( $_gate == 'north' ) $result = array( array(
  'Counter',
  'North #1',
  'North #2',
  'North #3',
  'North #4',
  array( 'role'=>'annotation' ),
) );
elseif( $_gate == 'south' ) $result = array( array(
  'Counter',
  'South #1',
  'South #2',
  'South #3',
  'South #4',
  array( 'role'=>'annotation' ),
) );

$data = array( 'northgate'=>array(), 'southgate'=>array());
if( $type == 'hour' ) {
  $day = $_REQUEST['day'];

  $sql = "SELECT * FROM gate_counter_hour WHERE date = ".$db->qstr( $day )." ORDER BY hour ASC, gate ASC";
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    $gate = $row['gate'];
    $hour = $row['hour'];
    $data[$gate][$hour] = array( intval( $row['diff1'] ), intval( $row['diff2'] ), intval( $row['diff3'] ), intval( $row['diff4'] ));
  }
  $rs->Close();

  for( $i = 0; $i < 24; $i++ ) {
    $arr_n = array_key_exists( $i, $data['northgate'] ) ? $data['northgate'][$i] : array( 0, 0, 0, 0);
    $arr_s = array_key_exists( $i, $data['southgate'] ) ? $data['southgate'][$i] : array( 0, 0, 0, 0);

    if( $_gate == null ) $result[] = array_merge( array( "{$i}" )
        , $arr_n
        , $arr_s
        , array( array_sum( array_merge( $arr_n, $arr_s )) == 0 ? '' : array_sum( array_merge( $arr_n, $arr_s )).'' ));
    elseif( $_gate == 'north' ) $result[] = array_merge( array( "{$i}" )
        , $arr_n
        , array( array_sum( $arr_n ) == 0 ? '' : array_sum( $arr_n ).'' ));
    elseif( $_gate == 'south' ) $result[] = array_merge( array( "{$i}" )
        , $arr_s
        , array( array_sum( $arr_s ) == 0 ? '' : array_sum( $arr_s ).'' ));
  }
}
elseif( $type == 'week' ) {
  $week = $_REQUEST['week'];
  $year = $_REQUEST['year'];

  $sql = "SELECT * FROM gate_counter_day WHERE week = ".$db->qstr( $week )." ORDER BY date ASC, gate ASC";
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    $gate = $row['gate'];
    $date = $row['date'];
    $data[$gate][$date] = array( intval( $row['diff1'] ), intval( $row['diff2'] ), intval( $row['diff3'] ), intval( $row['diff4'] ));
  }
  $rs->Close();


  $dto = new \DateTime();
  $dto->setISODate($year, $week);
  for( $i = 0; $i < 7; $i++ ) {
    $day = $dto->format('Y-m-d');
    if( $_gate == null ) $result[] = array_merge( array( "{$day}" )
        , array_key_exists( $day, $data['northgate'] ) ? $data['northgate'][$day] : array( 0, 0, 0, 0)
        , array_key_exists( $day, $data['southgate'] ) ? $data['southgate'][$day] : array( 0, 0, 0, 0)
        , array( '' ));
    elseif( $_gate == 'north' ) $result[] = array_merge( array( "{$day}" )
        , array_key_exists( $day, $data['northgate'] ) ? $data['northgate'][$day] : array( 0, 0, 0, 0)
        , array( '' ));
    if( $_gate == 'south' ) $result[] = array_merge( array( "{$day}" )
        , array_key_exists( $day, $data['southgate'] ) ? $data['southgate'][$day] : array( 0, 0, 0, 0)
        , array( '' ));
    $dto->modify('+1 days');
  }

}
elseif( $type == 'days' ) {
  $begin = $_REQUEST['begin'];
  $end = $_REQUEST['end'];

  $sql = "SELECT * FROM gate_counter_day WHERE date >= ".$db->qstr( $begin )." AND date <= ".$db->qstr( $end )." ORDER BY date ASC, gate ASC";
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    $gate = $row['gate'];
    $date = $row['date'];
    $data[$gate][$date] = array( intval( $row['diff1'] ), intval( $row['diff2'] ), intval( $row['diff3'] ), intval( $row['diff4'] ));
  }
  $rs->Close();

  $start = new \DateTime( $begin );
  $stop = new \DateTime( $end );

  while( $start <= $stop ) {
    $day = $start->format('Y-m-d');
    $arr_n = array_key_exists( $day, $data['northgate'] ) ? $data['northgate'][$day] : array( 0, 0, 0, 0);
    $arr_s = array_key_exists( $day, $data['southgate'] ) ? $data['southgate'][$day] : array( 0, 0, 0, 0);
    if( $_gate == null ) $result[] = array_merge( array( "{$day}" )
        , $arr_n
        , $arr_s
        , array( array_sum( array_merge( $arr_n, $arr_s )) ));
    elseif( $_gate == 'north' ) $result[] = array_merge( array( "{$day}" )
        , $arr_n
        , array( array_sum( $arr_n ) ));
    elseif( $_gate == 'south' ) $result[] = array_merge( array( "{$day}" )
        , $arr_s
        , array( array_sum( $arr_s ) ));
    $start->modify('+1 days');
  }

}

echo json_encode( $result );
?>
