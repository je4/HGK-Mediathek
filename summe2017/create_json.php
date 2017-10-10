<?php
namespace Mediathek;

header( 'Content-type: application/json' );

include( '../init.inc.php' );

$d2 = json_decode( file_get_contents('data.json'), true);

$data = array();
$sql = "SELECT formid FROM application.form WHERE status=".$db->qstr( 'upload' );
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $formid = $row['formid'];
  $d = array();
  $sql = "SELECT * FROM application.formdata WHERE formid=".$formid;
  $h = array();
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $h[$row2['name']] = $row2['value'];
  }
  $rs2->Close();
  $d[] = array_key_exists( $formid, $d2 );
  $d[] = $h['nachname'].', '.$h['vorname'];
  $d[] = array_key_exists( 'werkjahr', $h) ? $h['werkjahr'] : null;
  $d[] = array_key_exists( 'titel', $h) ? $h['titel'] : null;
  $d[] = array_key_exists( 'art', $h) ? $h['art'] : null;
  $d['formid'] = $formid;
  if( array_key_exists( $formid, $d2 )) $d['info'] = $d2[$formid];
  //$d[] = array_key_exists( 'dauer', $h) ? $h['dauer'] : null;
  $data[] = $d;
}
$rs->Close();

echo json_encode( array( 'data'=>$data,
'recordsTotal'=>count($data),
'recordsFiltered'=>count($data)
));
