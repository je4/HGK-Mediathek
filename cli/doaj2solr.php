<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';


$doajClient = new \Phpoaipmh\Client('https://doaj.org/oai');
$doajEndpoint = new \Phpoaipmh\Endpoint($doajClient, \Phpoaipmh\Granularity::DATE_AND_TIME);

$entity = new DOAJEntity( $pg );
$solr = new SOLR( $solrclient );

$result = $doajEndpoint->identify();

$sql = "SELECT to_char(datestamp, 'YY-MM-DD\"T\"HH24:MI:SS\"Z\"') FROM oai_pmh WHERE oai_pmh_source_id=1 ORDER BY datestamp DESC";
echo $sql."\n";
$datestamp = $pg->GetOne( $sql );
if( $datestamp == null ) $datestamp = (string) $result->Identify->earliestDatestamp;
//$datestamp = '1970-09-08T01:30:57Z';

$recs = $doajEndpoint->listRecords( 'oai_dc', new \DateTime( $datestamp ));
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
//		  print_r( $val );
          if( !@is_array( $dc[$val['tag']] )) $dc[$val['tag']] = array();
            $dc[$val['tag']][] = $val['value'];
      }
    }
	
    $identifier = $dc['IDENTIFIER'][0];
	if( !$identifier ) { 
		echo "empty identifier\n";
		continue;
	}
    $datestamp = $dc['DATESTAMP'][0];
    $type = $dc['DC:TYPE'][0];
    echo $identifier.": ".$datestamp."\n";
	print_r( $dc );
	$json = json_encode( $dc );
	$sql = "INSERT INTO oai_pmh
			  VALUES( ".$pg->qstr( $identifier ).", ".$pg->qstr( $datestamp ).", ".$pg->qstr( 'journal' ).", ".$pg->qstr( $json ).", 1 )
			ON CONFLICT (identifier) DO UPDATE
				SET datestamp=".$pg->qstr( $datestamp ).", type=".$pg->qstr( 'journal' ).", data=".$pg->qstr( $json ).", oai_pmh_source_id=1;";
	//echo "\n------\n{$sql}\n--------\n";
	$pg->Execute( $sql );
	
	$entity->loadFromDatabase( $identifier, 'doajarticle' );
    $solr->import( $entity );
	//if( $counter++ > 10 ) exit;
}


exit;



$result = $doajEndpoint->identify();
//var_dump($result);

$sql = "SELECT to_char(datestamp, 'YY-MM-DDTHH24:MI:SSZ') FROM oai_pmh WHERE oai_pmh_source_id=1 ORDER BY datestamp DESC";
echo $sql."\n";
$datestamp = $db->GetOne( $sql );
if( $datestamp == null ) $datestamp = (string) $result->Identify->earliestDatestamp;
// var_dump( $datestamp );
//$datestamp = "2016-08-08T15:46:18Z";
echo "Starting with: ".$datestamp."\n";
$recs = $doajEndpoint->listRecords( 'oai_dc', new \DateTime( $datestamp ) /*, new \DateTime( '2016-09-08T01:30:57Z' ) */);
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
	if( !$identifier ) { 
		echo "empty identifier\n";
		continue;
	}
    $datestamp = $dc['DATESTAMP'][0];
    $type = $dc['DC:TYPE'][0];
    echo $identifier.": ".$datestamp."\n";
    $sql = "REPLACE INTO source_doajoai( identifier, datestamp, type, data )
        VALUES (".$db->qstr( $identifier ).", ".$db->qstr( $datestamp ).", ".$db->qstr( $type ).", ".$db->qstr( json_encode( $dc )).")";
    $db->Execute( $sql );
    
    $entity->loadFromDatabase( $identifier, 'doajarticle' );
    $solr->import( $entity );
//    if( $counter++ > 10 ) exit;
}

exit;


/*
// Results will be iterator of SimpleXMLElement objects
$results = $doajEndpoint->listMetadataFormats();
foreach($results as $item) {
    var_dump($item);
}
*/

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

$counter = 0;
$sql = "SELECT `identifier` as id FROM source_doajoai WHERE type=".$db->qstr( 'article' );
echo $sql."\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];
    if( !strlen( $sys )) continue;
    
    try {
      $entity->loadFromDatabase( $sys, 'doajarticle' );
      
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
    catch( Exception $ex ) {
      echo $ex;
    }

    //if( $count++ > 10 ) break;
}
$rs->Close();
?>