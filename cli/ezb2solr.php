<?php

namespace Mediathek;

include '../init.inc.php';

/**
 * wget -O ezb.csv --no-check-certificate 'https://lurk:rechner987@ezb.uni-regensburg.de/admin/titlelist.php?bibid=FHNW&lang=de&notations%5B%5D=ALL&colors%5B%5D=1&colors%5B%5D=2&colors%5B%5D=6&type%5B%5D=1&type%5B%5D=2&type%5B%5D=9&type%5B%5D=11&price_type%5B%5D=1&price_type%5B%5D=2&price_type%5B%5D=3&price_type%5B%5D=4&price_type%5B%5D=5&rest_id%5B%5D=1&rest_id%5B%5D=2&rest_id%5B%5D=3&rest_id%5B%5D=4&publisher=&input_date=&input_date_upto=&last_change=&zdb_id%5B%5D=Y&zdb_id%5B%5D=N&outfields%5B%5D=title&outfields%5B%5D=color&outfields%5B%5D=eissn&outfields%5B%5D=url&outfields%5B%5D=publisher&outfields%5B%5D=type&outfields%5B%5D=pissn&outfields%5B%5D=special_url&outfields%5B%5D=discipline&outfields%5B%5D=price_type&outfields%5B%5D=zdb_id&outfields%5B%5D=frontdoor_url&outfields%5B%5D=period&outfields%5B%5D=rest_id&outfields%5B%5D=jour_id&outfields%5B%5D=warpto_url&sortorder=uns&output_format=tsv&firstcol=title'
 */

$entity = new EZBEntity( $db );
$solr = new SOLR( $solrclient );

if(( $handle = fopen( "ezb.csv", "r" )) === false ) {
    echo "fehler beim oeffnen von ezb.csv\n";
    exit;
}

$titles = null;
while (($data = fgetcsv($handle, 0, "\t"))) {
    // print_r( $data );
    if( count( $data ) < 10 ) continue;
    
    if( $titles == null ) {
        $titles = $data;
        continue;
    }
    
    $row = array();
    foreach( $data as $key=>$value ) {
        $row[$titles[$key]] = utf8_encode( $value );
    }
//    var_dump( $row );
    
//    exit;
    
    $entity->loadFromArray( $row, 'ezb' );
    
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
fclose( $handle );
?>