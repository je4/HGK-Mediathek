<?php

namespace Mediathek;

include '../init.inc.php';


$entity = new DOAJArticleEntity( $db );
$solr = new SOLR( $solrclient );

$doajClient = new \Phpoaipmh\Client('http://www.doaj.org/oai.article');
$doajEndpoint = new \Phpoaipmh\Endpoint($doajClient);

$result = $doajEndpoint->identify();
var_dump($result);

$sql = "SELECT DATE_FORMAT(datestamp, '%Y-%m-%dT%H:%i:%sZ') FROM source_doajoai ORDER BY datestamp DESC";
$datestamp = $db->GetOne( $sql );
if( $datestamp == null ) $datestamp = (string) $result->Identify->earliestDatestamp;
// var_dump( $datestamp );
echo "Starting with: ".$datestamp."\n";

$recs = $doajEndpoint->listRecords( 'oai_dc');
$counter = 0;
foreach( $recs as $rec ) {
    var_dump( $rec );
    exit;
}

/*
// Results will be iterator of SimpleXMLElement objects
$results = $doajEndpoint->listMetadataFormats();
foreach($results as $item) {
    var_dump($item);
}
*/


exit;

//$sys = '006931662';
//$sys = '5141218';
//$sys = '001874213';
$sys = '009526976';

/*
$sql = "SELECT * FROM nebis_grab2";
$rs = $db->Execute( $sql );
$x = 0;
foreach( $rs as $row ) {
    $sql = "INSERT INTO nebis_grab VALUES( ".$db->qstr( $row['sys']).",".$db->qstr( utf8_encode( $row['tag'])).",".$db->qstr( utf8_encode( $row['value']))." );";
    $db->Execute( $sql );
    $x = ($x+1)%100;
    if( $x == 0 ) echo '.';
}
$rs->Close();
exit;
*/


$sql = "SELECT DISTINCT `Journal EISSN (online version)` as id FROM source_doaj";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];
    if( !strlen( $sys )) continue;
    
    $entity->loadFromDatabase( $sys, 'doaj' );
    
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