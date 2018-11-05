<?php

namespace Mediathek;

include '../init.inc.php';

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';
//$groups = array( 1387750, 1510019, 1510009, 1624911, 1803850, 2061687 );
$groups = array( 1510009 ); // Institut Kunst
//$groups = array( 1624911 ); // IKUVid
//$groups = array( 2068924 ); // summe 2017 2068924
//$groups = array( 2180340 ); // grenzgang
//$groups = array( 2171463, );  // anfaenge der kuenstlerischen forschung
//$groups = array( 2206003, );  // act
//$groups = array( 1510019, );  // Hyperwerk
//$groups = array( 2066935, );  // RIMAB
//$groups = array( 2171465, );  // Basle Bibliography for Historical Performance Practice


//$groups = array( 1387750, 1510019, 1510009, 1624911, 1803850, 2061687, 2066935, 1624911, 2068924, 2180340, 2206003 );

$solr = new SOLR( $solrclient, $db );

$zotero = new Zotero( $db, $config['zotero']['apiurl'], $config['zotero']['apikey'], $config['zotero']['mediapath'], STDOUT );
try {
  foreach( $groups as $group ) {
        $zotero->syncItems( $group );
  }
} catch (\ADODB_Exception $e) {
  var_dump($e);
  adodb_backtrace($e->gettrace());
}

$entity = new zoteroEntity( $db );
$cnt = 0;
foreach( $groups as $group ) {
  foreach( $zotero->loadChildren( $group ) as $item ) {
    echo $item; if( $item->isTrashed()) echo "  [TRASH]\n";
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
