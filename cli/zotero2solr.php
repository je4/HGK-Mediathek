<?php

namespace Mediathek;

include '../init.inc.php';

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';
$apiurl = 'https://api.zotero.org';
$apikey = 'XxuGdxZuXiB1epXH8B9XX2oR';
$groups = array( 1387750 );

$solr = new SOLR( $solrclient );


$zotero = new Zotero( $db, $apiurl, $apikey );

foreach( $groups as $group ) {
  $zotero->syncItems( $group );
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
