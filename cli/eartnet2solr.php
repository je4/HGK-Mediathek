<?php

namespace Mediathek;

include '../init.inc.php';

function BMDM_autoload( $class ) {
    $file = '../lib/'.str_replace( '\\', '/', $class ).'.php';
    $file = str_replace( 'dautkom/bmdm/', 'BMDM/', $file );
    //echo $file . "\n";
    if( file_exists( $file )) include( $file );
}
spl_autoload_register('Mediathek\BMDM_autoload');

function cleanName( $name ) {
  $name = trim( preg_replace(
      array( '/, .+$/', '/\(.+\)/', '/[^a-zA-Z0-9 ]/' ),
      array( ''       , ''        , '' ),
      strtolower( iconv("UTF-8", "ASCII//TRANSLIT", $name ))));
  return $name;
}


$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';

$bmdm = new \dautkom\bmdm\BMDM();

$sql0 = "SELECT person_id, person_vorname, person_nachname
          FROM european_art_net.cont_person
          WHERE bmdm_name IS NULL
          LIMIT 0, 10000";
do {
  echo "{$sql0}\n";
  $rows = $db->GetAll( $sql0 );
  $continue = false;
  foreach( $rows as $row ) {
    $continue = true;
    $name = cleanName( "{$row['person_vorname']} {$row['person_nachname']}" );
    echo "[{$row['person_id']}] {$name}\n";
    if( true ) {
      $bmdm_name = $name;
    }
    else {
      $bmdm_vorname = $bmdm->set(cleanName( $row['person_vorname']))->soundex();
      $bmdm_nachname = $bmdm->set(cleanName( $row['person_nachname']))->soundex();
      $bmdms = array();
      foreach( array_merge( $bmdm_vorname['numeric'], $bmdm_nachname['numeric'] ) as $numeric ) {
        $bmdms[] = $numeric[0];
      }
      $bmdm_name = implode( '.', $bmdms );
    }
    //$bmdm_name = $bmdm->set("{$row['person_vorname']} {$row['person_nachname']}")->soundex();
    $sql = "UPDATE european_art_net.cont_person
      SET bmdm_name=".$db->qstr( $bmdm_name )."
        WHERE person_id=".$db->qstr( $row['person_id']);
    echo "{$sql}\n";
    $db->Execute( $sql );
  }
  echo "loop done {$continue}\n";
} while( $continue );


$entity = new eartnetEntity( $db );
$solr = new SOLR( $solrclient, $db );

$sql = "SELECT DISTINCT bmdm_name FROM european_art_net.cont_person p WHERE  p.bmdm_name <> ''";
echo "{$sql}\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $sql = "SELECT * FROM european_art_net.cont_person p, european_art_net.cont_datenbank d
            WHERE p.person_id_datenbank=d.datenbank_id AND p.bmdm_name=".$db->qstr( $row['bmdm_name'] );
//  $sql = "SELECT * FROM european_art_net.cont_person p, european_art_net.cont_datenbank d
//            WHERE p.person_id_datenbank=d.datenbank_id AND p.person_kategorie_2=".$db->qstr( $row['person_kategorie_2'] )." AND p.bmdm_name=".$db->qstr( $row['bmdm_name'] );
  echo "{$sql}\n";
  $rs2 = $db->Execute( $sql );
  $data = array();
  foreach( $rs2 as $row2 ) {
    $data[] = $row2;
  }
  $rs2->Close();
  if( count( $data ) == 0 ) continue;
  $entity->loadFromArray( $data );
  $solr->import( $entity );
}
$rs->Close();

$update = $solrclient->CreateUpdate();
$update->addCommit();
$result = $solrclient->update( $update );
