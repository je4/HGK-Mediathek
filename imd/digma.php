<?php

namespace Mediathek;

include '../init.inc.php';

$fname = 'digma.gv';

$fstr = "graph D {\n";

$sql = "SELECT DISTINCT A FROM imd.digma";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $a = $row['A'];
  $str = "      label = \"{$a}\";\n";
  $str .= "     node [style=filled];\n";
  $str .= "     color=blue;\n";
  $sql = "SELECT * FROM imd.digma WHERE A=".$db->qstr( $a );
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    //$a = $row2['A'];
    $b = $row2['B'];
    $c = $row2['C'];
    $d = $row2['D'];
    $e = $row2['E'];
    $f = $row2['F'];
    $str .= "      md5_".md5($b)." [label=\"{$b}\"];\n";
  }
  $rs2->Close();
  $str = "   subgraph {$a} {\n{$str}\n";
  $str .= "   }\n";
  $fstr .= $str;
}
$rs->close();

$sql = "SELECT * FROM imd.digma";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $a = $row['A'];
  $b = $row['B'];
  $c = $row['C'];
  $d = $row['D'];
  $e = $row['E'];
  $f = $row['F'];
  if( $d ) $fstr .= "   md5_".md5($b)." -- $d;\n";
  if( $e ) $fstr .= "   md5_".md5($b)." -- $e;\n";
  if( $f ) $fstr .= "   md5_".md5($b)." -- $f;\n";
}
$rs->Close();

$fstr .= "}\n";

file_put_contents( $fname, $fstr );
