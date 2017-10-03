<?php

namespace Mediathek;

include '../init.inc.php';

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';

$entity = new IKUVidEntity( $db );
$solr = new SOLR( $solrclient );


$sql = "SELECT DISTINCT `Archiv-Nr` as id FROM source_ikuvid";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];

    $entity->loadFromDatabase( $sys, 'ikuvid' );
    $sql = "REPLACE INTO mediathek_handle.handles_handle (`handle`, `idx`, `type`, `data`, `ttl_type`, `ttl`, `timestamp`, `refs`, `admin_read`, `admin_write`, `pub_read`, `pub_write`)
        VALUES (".$db->qstr( $hdlprefix.$entity->getID()).", '1', 'URL', ".$db->qstr( $urlbase.$entity->getID()).", 0, 86400, NULL, NULL, 1, 0, 1, 0)"; 
    $db->execute( $sql );
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