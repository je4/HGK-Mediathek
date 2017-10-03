<?php

$md5 = $_GET['form'];

if( !is_dir( "personpics/{$md5}" )) return;

$files = array();
$d = dir( "personpics/{$md5}" );
while( false !== ($entry = $d->read())) {
  if( $entry{0} == '.' ) continue;
  $files[] = $entry;
}

sort( $files );

$lasttype = 'small';
foreach( $files as $entry ) {
  if( preg_match( "/_(wide|small)\.png$/", $entry, $matches )) {
//    echo "{$lasttype} -> {$matches[1]} ";
    if( $lasttype == 'wide' && $matches[1] == 'small' ) {
      echo "<br />\n";
    }
    $lasttype = $matches[1];
    echo "<img src=\"personpics/{$md5}/{$entry}\" height=200>\n";
  }
}
 ?>
