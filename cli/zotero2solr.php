<?php

namespace Mediathek;

include '../init.inc.php';

$cleanup = true;

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';
$groups = array(
// 1510009,  // Institut Kunst
// 1624911,  // IKUVid
 //// 2068924,  // summe 2017 2068924
// 2180340,  // grenzgang
 //// 2171463,   // anfaenge der kuenstlerischen forschung
//2206003,   // act
// 2260611,  // DigitaleSee
 //// 1803850, // Kasko
 ///// 1624911, // PCB Basel
//1510019,   // Hyperwerk
// 2066935,   // RIMAB
// 2171465,   // Basle Bibliography for Historical Performance Practice
// 2250437, // Grenzgang (neu)
// 2315925, // Integrative Gestaltung / Masterstudio
//2317722, // Archive des Ephemeren
// 2260611, // feministisches Improvisatorium
//2327162, // Digital Brainstorming
//2329831, // HGK Fotos
2358931, // Julia
);

//$groups = array( 1387750, 1510019, 1510009, 1624911, 1803850, 2061687, 2066935, 1624911, 2068924, 2180340, 2206003 );



$solr = new SOLR( $solrclient, $db );

$entity = new zoteroEntity( $db );
$cnt = 0;

$zotero = new Zotero( $db, $config['zotero']['apiurl'], $config['zotero']['apikey'], $config['zotero']['mediapath'], STDOUT );

if( true ) {

if( $cleanup ) {
  foreach( $groups as $group ) {
    $sql = "DELETE FROM zotero.groups WHERE id='{$group}'";
    echo $sql."\n";
    $db->Execute( $sql );
  }
}

  foreach( $groups as $group ) {
    try {
        $zotero->syncItems( $group );
    } catch (\Exception $e) {
      var_dump($e);
    }
  }

// die();
}

$entity = new zoteroEntity( $db );
$cnt = 0;
foreach( $groups as $group ) {
  if( $cleanup ) {
	  echo "deleting id:zotero-{$group}.*\n";
    $update = $solrclient->CreateUpdate();
    $update->addDeleteQuery("id:zotero-{$group}.*");
	$update->addCommit();
    $result = $solrclient->update( $update );
  }
  
  foreach( $zotero->loadChildren( $group ) as $item ) {
    echo $item;

    if( $item->isTrashed()) echo "  [TRASH]\n";
    $entity->reset();
    $data = $item->getData();
//    var_dump( $data );
    $entity->loadFromArray( $data );
    $title = $entity->getTitle();
    echo "Title: ".$title."\n";
    if( $item->isTrashed()) {
      $solr->delete( $entity->getID() );
    }
    else {
      $solr->import( $entity );
    }
    echo "{$cnt}\n";
    $cnt++;
  }
}
$update = $solrclient->CreateUpdate();
$update->addCommit();
$result = $solrclient->update( $update );
