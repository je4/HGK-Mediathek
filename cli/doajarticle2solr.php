<?php

namespace Mediathek;

include '../init.inc.php';



$doajClient = new \Phpoaipmh\Client('http://www.doaj.org/oai.article');
$doajEndpoint = new \Phpoaipmh\Endpoint($doajClient, \Phpoaipmh\Granularity::DATE_AND_TIME);

$result = $doajEndpoint->identify();
//var_dump($result);

$sql = "SELECT DATE_FORMAT(datestamp, '%Y-%m-%dT%H:%i:%sZ') FROM source_doajoai WHERE type=\"article\" ORDER BY datestamp DESC";
echo $sql."\n";
$datestamp = $db->GetOne( $sql );
if( $datestamp == null ) $datestamp = (string) $result->Identify->earliestDatestamp;
// var_dump( $datestamp );
echo "Starting with: ".$datestamp."\n";

$recs = $doajEndpoint->listRecords( 'oai_dc');
$counter = 0;
foreach( $recs as $xmlrec ) {
//    var_dump( $rec );
    $xml = str_replace( '<record', '<record xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', $xmlrec );
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
    $type = $dc['DC:TYPE'][0];
    echo $identifier."\n";
    $sql = "REPLACE INTO source_doajoai( identifier, datestamp, type, data )
        VALUES (".$db->qstr( $identifier ).", ".$db->qstr( $datestamp ).", ".$db->qstr( $type ).", ".$db->qstr( json_encode( $dc )).")";
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


exit;

$entity = new DOAJArticleEntity( $db );
$solr = new SOLR( $solrclient );

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