<?php

namespace Mediathek;

include '../init.inc.php';


$entity = new IKUVidEntity( $db );
$solr = new SOLR( $solrclient );


$sql = "SELECT DISTINCT `Archiv-Nr` as id FROM source_ikuvid";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];

    $entity->loadFromDatabase( $sys, 'ikuvid' );
    
    //$xml = $entity->getXML();
    //echo $xml->saveXML();
    
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
    
    $loans = $entity->getLoans();
    echo "Loans: ";
    print_r( $loans );
    
    $licenses = $entity->getLicenses();
    echo "Licenses: ";
    print_r( $licenses );
    
    $signatures = $entity->getSignatures();
    echo "Signatures: ";
    print_r( $signatures );
    
    $urls = $entity->getURLs();
    echo "URLs: ";
    print_r( $urls );
    
    $solr->import( $entity );

}
$rs->Close();
?>