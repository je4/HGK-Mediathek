<?php

namespace Mediathek;

include '../init.inc.php';



$doabClient = new \Phpoaipmh\Client('http://www.doabooks.org/oai');
$doabEndpoint = new \Phpoaipmh\Endpoint($doabClient, \Phpoaipmh\Granularity::DATE_AND_TIME);

$result = $doabEndpoint->identify();
//var_dump($result);

$sql = "SELECT DATE_FORMAT(datestamp, '%Y-%m-%dT%H:%i:%sZ') FROM source_doajoai WHERE identifier LIKE \"oai:doab-books:%\" ORDER BY datestamp DESC";
$datestamp = $db->GetOne( $sql );
if( $datestamp == null ) $datestamp = (string) $result->Identify->earliestDatestamp;
// var_dump( $datestamp );
echo "Starting with: ".$datestamp."\n";

$recs = $doabEndpoint->listRecords( 'oai_dc', new \DateTime( $datestamp ));
$counter = 0;
foreach( $recs as $xmlrec ) {
//    var_dump( $rec );
    $xml = $xmlrec; // str_replace( '<record', '<record xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', $xmlrec );
//    echo "\n".$xml."\n";
    $p = xml_parser_create();
    xml_parse_into_struct( $p, $xml, $vals, $index /*, LIBXML_NOWARNING */);
    xml_parser_free($p);
    $dc = array();
//    print_r( $vals );
    foreach( $vals as $val ) {
      if( $val['type'] == 'complete' )
      {
          if( !@is_array( $dc[$val['tag']] )) $dc[$val['tag']] = array();
            $dc[$val['tag']][] = $val['value'];
      }
    }
    $identifier = $dc['IDENTIFIER'][0];
    $datestamp = $dc['DATESTAMP'][0];
    echo $identifier."\n";
    $sql = "REPLACE INTO source_doajoai( identifier, datestamp, type, data )
        VALUES (".$db->qstr( $identifier ).", ".$db->qstr( $datestamp ).", ".$db->qstr( "book" ).", ".$db->qstr( json_encode( $dc )).")";
    $db->Execute( $sql );                                                                                                           
//    if( $counter++ > 10 ) exit;
}

/*
// Results will be iterator of SimpleXMLElement objects
$results = $doajEndpoint->listMetadataFormats();
foreach($results as $item) {
    var_dump($item);
}
*/


//exit;

$entity = new DOABEntity( $db );
$solr = new SOLR( $solrclient );

$sql = "SELECT DISTINCT `identifier` as id FROM source_doajoai WHERE identifier LIKE ".$db->qstr( 'oai:doab-books:%' );
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];
    if( !strlen( $sys )) continue;
    
    $entity->loadFromDatabase( $sys, 'doab' );
    
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

    $licenses = $entity->getLicenses();
    echo "Licenses: ";
    print_r( $licenses );
    
    $signatures = $entity->getSignatures();
    echo "Signatures: ";
    print_r( $signatures );
    
    $urls = $entity->getURLs();
    echo "URLs: ";
    print_r( $urls );
    if( strlen( trim( $entity->getTitle())))
       $solr->import( $entity );

}
$rs->Close();
?>