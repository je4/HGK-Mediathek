<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$sourceid=4;

$doabClient = new \Phpoaipmh\Client('http://oai.swissbib.ch:20103/oai/DB=2.1/');
$doabEndpoint = new \Phpoaipmh\Endpoint($doabClient, \Phpoaipmh\Granularity::DATE_AND_TIME);

$entity = new swissbibEntity();
$solr = new SOLR( $solrclient );

$result = $doabEndpoint->identify();

$sql = "SELECT to_char( datestamp, 'YYYY-MM-DDXHH24:MI:SS') FROM oai_pmh_source WHERE oai_pmh_source_id={$sourceid}";
$datestamp = $pg->GetOne( $sql );

var_dump( $result->Identify );

if( $datestamp == null )
	$datestamp = (string) $result->Identify->earliestDatestamp;
$datestamp = str_replace( 'X', 'T', $datestamp );

echo "Starting with: ".$datestamp."\n";
try {
	$recs = $doabEndpoint->listRecords( 'm21-xml/oaitp', new \DateTime( $datestamp ));
	$counter = 0;
	foreach( $recs as $xmlrec ) {
		$counter++;
		
		try {
		
		    $xml = str_replace( '<record', '<record xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', $xmlrec );
		
			$record = new OAIPMHRecord( $xml );
			
			$id = $record->getIdentifier();
			$datestamp = $record->getDatestamp();
			echo "> {$id} {$datestamp}({$counter})\n";
		  
			if( $record->isDeleted() ) {
				echo "   deleting...\n";
				$solr->delete( 'swissbib-'.$id);
				$pg->Execute($sql);
				continue;
			}
		
			$metadata = $record->getMetadata();
			
			$data = array();
			$data['IDENTIFIER'] = array( $id );
			$data['DATESTAMP'] = array( $record->getDatestamp() );
			
			//echo $metadata->ownerDocument->saveXML($metadata)."\n";
			$marc = null;
			foreach( $metadata->childNodes as $child ) {
				if( is_a( $child, 'DOMElement' ) && $child->tagName == 'mx:record' ) {
					$marc  = $child;
				}
			}
			
			if( $marc == null ) continue;
			
			$entity->loadNode( $id, $marc, 'swissbib' );
			echo $entity->getTitle()."\n";
			/*
			if( count($entity->getSourceIDs())) {
				foreach( $entity->getSourceIDs() as $tag) echo $tag."\n";
				echo "\n{$xml}\n";
				exit;
			}
			continue;
			*/
			
			//echo implode( ';', $entity->getGeneralNote())."\n";
		/*	
			foreach( $entity->getTags() as $tag) echo $tag."\n";
			foreach( $entity->getCluster() as $tag) echo $tag."\n";
			foreach( $entity->getAuthors() as $tag) echo $tag."\n";
			echo implode( '/', $entity->getPublisher()).', '
				.$entity->getCity().', '
				.$entity->getYear()."\n";
			
			foreach( $entity->getSignatures() as $tag) echo $tag."\n";
			foreach( $entity->getSourceIDs() as $tag) echo $tag."\n";
			foreach( $entity->getCodes() as $tag) echo $tag."\n";
		*/	
		
		/*	
			$json = json_encode( $data );
			$sql = "INSERT INTO oai_pmh
					  VALUES( ".$pg->qstr( $record->getIdentifier() ).", ".$pg->qstr( $record->getDatestamp() ).", ".$pg->qstr( 'article' ).", ".$pg->qstr( $json ).", {$sourceid} )
					ON CONFLICT (identifier) DO UPDATE
						SET datestamp=".$pg->qstr( $datestamp ).", type=".$pg->qstr( 'article' ).", data=".$pg->qstr( $json ).", oai_pmh_source_id={$sourceid}";
			//echo "\n------\n{$sql}\n--------\n";
			$pg->Execute( $sql );
		*/	
			$solr->import( $entity );
			
			if( $counter % 1000 == 0 ) {
				$sql = "UPDATE oai_pmh_source SET datestamp=".$pg->qstr( $record->getDatestamp() )." WHERE oai_pmh_source_id={$sourceid}";
				echo "\n------\n{$sql}\n--------\n";
				$pg->Execute( $sql );
			}
			
		//	if( $counter < 50010 ) continue;
		//	else exit;
			
		//	$entity->loadFromArray( $record->getIdentifier(), $data, 'doab' );
		}
		catch( \Exception $e ) {
			var_dump($e->getMessage());
			file_put_contents( "error.dat", print_r( $e->getMessage(), true )."\n".$e->getTraceAsString(), FILE_APPEND );
		}
	} 
}
catch( \Phpoaipmh\Exception\MalformedResponseException $e) {
	echo "Outer -------------------\n";
	echo print_r( $e->getMessage(), true )."\n".$e->getTraceAsString();
	
}

?>