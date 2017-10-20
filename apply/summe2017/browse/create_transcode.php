<?php

include( '../init.inc.php' );

$exportdir = '/data2/apply/summe2017/transcode.new/';
$basedir = '/data2/apply/summe2017/';

$done = json_decode( file_get_contents( '../../../summe2017/summe2017.json'), true );

$new = array();
foreach( $done['data'] as $d ) {
  if( !array_key_exists( 'info', $d )) $new[] = $d['formid'];
}

$sql = "SELECT * FROM form WHERE status='upload' and formid=101 ORDER BY formid ASC";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $formid = $row['formid'];
  if( array_search( $formid, $new ) === false ) continue;
  echo $formid."\n";
  $sql = "SELECT * FROM files WHERE (mimetype LIKE ".$db->qstr( 'application/octet%' ).' OR mimetype LIKE '.$db->qstr( 'video/%' ).") AND formid=".$formid;
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $target = str_replace( '..', '.', sprintf( '%03d_', $formid ).preg_replace(
      array('/[^a-zA-Z0-9 ._-]/'),
      array('_'), iconv('UTF-8', 'ASCII//TRANSLIT', $row2['name'])));
    $t = $exportdir.$target;
    $s = $basedir.$row2['filename'];
    $cmd = "ln ".escapeshellarg($s)." ".escapeshellarg($t);
    if( !file_exists( $t )) {
      echo $cmd."\n";
      `$cmd`;
    }
//    link( $t, $s);
  }
  $rs2->Close();
}
$rs->Close();
?>
