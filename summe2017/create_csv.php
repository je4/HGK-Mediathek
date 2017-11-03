<?php
namespace Mediathek;

header( 'Content-type: text/csv' );

include( '../init.inc.php' );

function excelEncode(&$value, $key)
{
  $value = iconv('UTF-8', 'Windows-1252', $value);

}

$fh = fopen( './summe2017.csv', 'w+' );
fputcsv( $fh, array( 'formid', 'name', 'werkjahr', 'jahrgang', 'titel', 'art', 'descr', 'web', 'dauer', 'img1', 'img2', 'img3' ), ';' );

$d2 = json_decode( file_get_contents('data.json'), true);

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
  $d = array();
  $d['formid'] = $formid;
  $name = trim( $h['vorname'].'  '.$h['nachname'], ' ,');
  if( array_key_exists( 'nachname2', $h )) $name .= ' / '.$h['vorname2'].' '.$h['nachname2'];
  if( array_key_exists( 'nachname3', $h )) $name .= ' / '.$h['vorname3'].', '.$h['nachname3'];
  $d['name'] = $name;
  $d['werkjahr'] = array_key_exists( 'werkjahr', $h) ? $h['werkjahr'] : '';
  $d['jahrgang'] = array_key_exists( 'jahrgang', $h) ? "({$h['jahrgang']})" : '';
  $d['titel'] = array_key_exists( 'titel', $h) ? $h['titel'] : '';
  $d['art'] = array_key_exists( 'art', $h) ? $h['art'] : '';
  $d['descr'] = array_key_exists( 'descr', $h) ? trim($h['descr']) : '';
  $d['web'] = array_key_exists( 'web', $h) ? $h['web'] : '';
  $d['dauer'] = array_key_exists( 'dauer', $h) ? $h['dauer'] : '';
  $pics = $d2[$formid][0]['mediums'];
  $num = count( $pics );
  $step = round( $num/4 );
  file_put_contents( 'cat.sh', 'ln '.$pics[$step].' cat/'.substr( $pics[$step], 9 )."\n", FILE_APPEND );
  file_put_contents( 'cat.sh', 'ln '.$pics[$step*2].' cat/'.substr( $pics[$step*2], 9 )."\n", FILE_APPEND );
  file_put_contents( 'cat.sh', 'ln '.$pics[$step*3].' cat/'.substr( $pics[$step*3], 9 )."\n", FILE_APPEND );
  $d['img1'] = 'cat/'.substr($pics[$step], 9 );
  $d['img2'] = 'cat/'.substr($pics[$step*2], 9 );
  $d['img3'] = 'cat/'.substr($pics[$step*3], 9 );



  //array_walk($d, 'excelEncode');
  fputcsv( $fh, $d, ';' );
}
$rs->Close();

fclose( $fh );

//readfile( '/tmp/summe2017.csv' );
