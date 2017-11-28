<?php

namespace Mediathek;

include '../init.inc.php';

$sql = "SELECT DISTINCT category FROM imd.cache_asset_person";
echo $sql."\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $cat = $row['category'];

  if( false ) {
    $sql = "select count(*) AS `weight`
      ,`ap1`.`category` AS `category`
      ,if(`ap1`.`personid`<`ap2`.`personid`,`ap1`.`personid`,`ap2`.`personid`) AS `p1id`
      ,if(`ap1`.`personid`<`ap2`.`personid`,`ap2`.`personid`,`ap1`.`personid`) AS `p2id`
    from (`imd`.`cache_asset_person` `ap1` join `imd`.`cache_asset_person` `ap2`)
     where ((`ap1`.`oid` = `ap2`.`oid`) and (`ap1`.`personid` <> `ap2`.`personid`)) and ap1.category=".$db->qstr( $cat )."
     group by `ap1`.`category`,p1id, p2id";
     $sql = "INSERT INTO imd.cache_category_graph ({$sql})";
     echo $sql."\n";
     $db->Execute( $sql );
     continue;
  }
  $fname = dirname( __FILE__ ).'/'.preg_replace(
    array('/[^a-zA-Z0-9 ._-]/'),
    array('_'), iconv('UTF-8', 'ASCII//TRANSLIT', $cat)).'.gv';
  if( file_exists( $fname )) unlink( $fname );
  $fp = fopen( $fname, 'w+' );
  fputs( $fp, "graph G {\n" );
  $sql = "SELECT *
    FROM imd.`cache_category_graph`
    WHERE category=".$db->qstr( $cat );
  echo $sql."\n";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $str = '   p'.str_replace( '-', '_', $row2['p1id'] ).' -- p'.str_replace( '-', '_', $row2['p2id'] ).' [weight='.$row2['weight'].'];';
    fputs( $fp, $str."\n" );
    //echo $str."\n";
  }
  $rs2->Close();
  $sql = "SELECT DISTINCT personid as pid, t.name as personname FROM imd.cache_asset_person cap, imd.tmp_thesaurus t WHERE t.pid=cap.personid AND category=".$db->qstr( $cat );
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $name = preg_replace( '/[\[\]"]/','', $row2['personname'] );
    $str = '   p'.str_replace( '-', '_', $row2['pid'] ).' [label="'.trim($name).'"]'.' ;';
    fputs( $fp, $str."\n" );
    //echo $str."\n";
  }
  $rs2->Close();

  fputs( $fp, "}\n" );
  fclose( $fp );
  $cmd = 'dot -Tsvg '.$fname.' -o'.$fname.'.svg';
  echo $cmd."\n";
  `$cmd`;
}
$rs->Close();
