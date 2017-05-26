<?php

namespace Mediathek;

include '../init.inc.php';

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';
$groups = array( 1387750 );

$solr = new SOLR( $solrclient );


$zotero = new Zotero( $db, $config['zotero']['apiurl'], $config['zotero']['apikey'], $config['zotero']['mediapath'], STDOUT );
try {
  foreach( $groups as $group ) {
        $zotero->syncItems( $group );
  }
} catch (\ADODB_Exception $e) {
  var_dump($e);
  adodb_backtrace($e->gettrace());
}
exit;

$entity = new videokunst_ch_workEntity( $db );


$cnt = 1;
foreach( $data['video'] as $video ) {
  $entity->reset();
  $entity->loadFromArray( $video );
  $title = $entity->getTitle();
  echo "Title: ".$title."\n";

  $tags = $entity->getTags();
  echo "Tags: ";
  print_r( $tags );

  $cluster = $entity->getCluster();
  echo "Cluster: ";
  print_r( $cluster );

  $authors = $entity->getAuthors();
  echo "Authors: ";
  print_r( $authors );

  $urls = $entity->getURLs();
  echo "URLs: ";
  print_r( $urls );

  $solr->import( $entity );
  echo "{$cnt}\n";
  $cnt++;
}
