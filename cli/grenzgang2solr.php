<?php

namespace Mediathek;

include '../init.inc.php';


$entity = new GrenzgangEntity( $db );
$solr = new SOLR( $solrclient, $db );


$sql = "SELECT DISTINCT `ID` as id FROM source_grenzgang"; // WHERE Berechtigung IS NOT NULL";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];

    echo "{$sys}\n";

    $entity->loadFromDatabase( $sys, 'grenzgang-' );

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
}
$rs->Close();

$update = $solrclient->CreateUpdate();
$update->addCommit();
$result = $solrclient->update( $update );
