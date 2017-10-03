<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$sourceid=4;

$doabClient = new \Phpoaipmh\Client('http://oai.swissbib.ch:20103/oai/DB=2.1/');
$doabEndpoint = new \Phpoaipmh\Endpoint($doabClient, \Phpoaipmh\Granularity::DATE_AND_TIME);

$entity = new swissbibEntity( $db );
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
	$counter = $interval = $interval2 =  0;
	foreach( $recs as $xmlrec ) {
		$counter++;
		$interval++;
		$interval2++;
		
		try {
		
		    $xml = str_replace( '<record', '<record xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', $xmlrec );
		
			$record = new OAIPMHRecord( $xml );
			
			$id = $record->getIdentifier();
			$datestamp = $record->getDatestamp();
			echo "> {$id} {$datestamp}({$counter})\n";
		  
			if( $record->isDeleted() ) {
				echo "   deleting swissbib-{$id}...\n";
				$solr->delete( 'swissbib-'.$id);
				//$pg->Execute($sql);
				//continue;
			} 
			else {
		
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
				
				//doConnectMySQL();
				$entity->loadNode( $id, $record, 'swissbib' );
				echo $entity->getTitle()."\n";
	
				do {
					try {
						$done = true;
						$solr->import( $entity );
					}
					catch( \ADODB_Exception $e ) {
						if( $done ) {
							$done = false;
							echo "> reconnect MySQL\n";
							doConnectMySQL( true );
						}
						else {
							throw $e;
						}
					}
				} while( !$done);
			}
			if( $interval2 >= 5000 ) {
				$interval2 = 0;
				
				echo "> commit...\n";
				$update = $solrclient->createUpdate();
				
				// add commit command to the update query
				$update->addCommit();
				
				// this executes the query and returns the result
				$result = $solrclient->update($update);
				
				$sql = "SELECT 1 as val";
				do {
					try {
						$done = true;
						$db->GetOne( $sql );
					}
					catch( \ADODB_Exception $e ) {
						if( $done ) {
							$done = false;
							echo "> reconnect MySQL\n";
							doConnectMySQL( true );
						}
						else {
							throw $e;
						}
					}
				} while( !$done);
				
				$sql = "UPDATE oai_pmh_source SET datestamp=".$pg->qstr( $record->getDatestamp() )." WHERE oai_pmh_source_id={$sourceid}";
				echo "\n------\n{$sql}\n--------\n";
				do {
					try {
						$done = true;
						$pg->Execute( $sql );
					}
					catch( \ADODB_Exception $e ) {
						if( $done ) {
							echo "> reconnect PostgreSQL\n";
							$done = false;
							doConnectPostgres( true );
						}
						else {
							throw $e;
						}
					}
				} while( !$done);
			}
			if( $interval > 500000 ) {
				$interval = 0;
				
				$update = $solrclient->createUpdate();
				
				// add commit command to the update query
				$update->addCommit();
				
				// this executes the query and returns the result
				$result = $solrclient->update($update);
				
				$sql = "UPDATE oai_pmh_source SET datestamp=".$pg->qstr( $record->getDatestamp() )." WHERE oai_pmh_source_id={$sourceid}";
				echo "\n------\n{$sql}\n--------\n";
				do {
					try {
						$done = true;
						$pg->Execute( $sql );
					}
					catch( \ADODB_Exception $e ) {
						if( $done ) {
							echo "> reconnect PostgreSQL\n";
							$done = false;
							doConnectPostgres( true );
						}
						else {
							throw $e;
						}
					}
				} while( !$done);
				
				echo "sleeping 5min\n";
				sleep( 5*60 );				
				
			}
			
		//	if( $counter < 50010 ) continue;
		//	else exit;
			
		//	$entity->loadFromArray( $record->getIdentifier(), $data, 'doab' );
		}
		
		catch( \Exception $e ) {
			var_dump($e->getMessage());
			file_put_contents( "error.dat", print_r( $e->getMessage(), true )."\n".$e->getTraceAsString(), FILE_APPEND );
			die();
		}
	} 
}
catch( \Phpoaipmh\Exception\MalformedResponseException $e) {
	echo "Outer -------------------\n";
	echo print_r( $e->getMessage(), true )."\n".$e->getTraceAsString();
	
}

doConnectMySQL();
?>