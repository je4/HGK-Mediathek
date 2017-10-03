<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$sourceid = 1;

$doajClient = new \Phpoaipmh\Client('https://doaj.org/oai');
$doajEndpoint = new \Phpoaipmh\Endpoint($doajClient, \Phpoaipmh\Granularity::DATE_AND_TIME);

$entity = new DOAJEntity( $pg );
$solr = new SOLR( $solrclient );

if( $argc == 1  ) {
  $result = $doajEndpoint->identify();

  //$sql = "SELECT DATE_FORMAT(datestamp, '%Y-%m-%dT%H:%i:%sZ') FROM source_doajoai WHERE identifier LIKE \"oai:doaj.org/journal:%\" ORDER BY datestamp DESC";
  $sql = "SELECT to_char( datestamp, 'YYYY-MM-DDXHH24:MI:SS') FROM oai_pmh WHERE oai_pmh_source_id={$sourceid} ORDER BY datestamp DESC";
  $datestamp = $pg->GetOne( $sql );
  if( $datestamp == null )
    $datestamp = (string) $result->Identify->earliestDatestamp;
  $datestamp = str_replace( 'X', 'T', $datestamp );
  // var_dump( $datestamp );
  echo "Starting with: ".$datestamp."\n";

  $recs = $doajEndpoint->listRecords( 'oai_dc', new \DateTime( $datestamp ));
  $counter = 0;
  foreach( $recs as $xmlrec ) {
      $xml = str_replace( '<record', '<record xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', $xmlrec );

  	$record = new OAIPMHRecord( $xml );

  	echo $record->getIdentifier()."\n";

  	if( $record->isDeleted() ) {
  		echo "   deleting...\n";
  		$solr->delete( 'doajarticle-'.$record->getIdentifier());
  		$sql = "DELETE FROM oai_pmg WHERE identifier=".$pg->qstr( $record->getIdentifier() );
  		$pg->Execute();
  		continue;
  	}

  	$metadata = $record->getMetadata();

  	$data = array();
  	$data['IDENTIFIER'] = array( $record->getIdentifier() );
  	$data['DATESTAMP'] = array( $record->getDatestamp() );

  //	echo $metadata->ownerDocument->saveXML($metadata)."\n";
  	$dc = $metadata->firstChild;
  	foreach( $dc->childNodes as $dca ) {
  	  //echo $dca->tagName.': '.$dca->nodeValue."\n";
  	  $tag = strtoupper( $dca->tagName );
  	  if( !array_key_exists( $tag, $data )) $data[$tag] = array();
  	  $data[$tag][] = $dca->nodeValue;
  	}

  	$json = json_encode( $data );
  	$sql = "INSERT INTO oai_pmh
  			  VALUES( ".$pg->qstr( $record->getIdentifier() ).", ".$pg->qstr( $record->getDatestamp() ).", ".$pg->qstr( 'journal' ).", ".$pg->qstr( $json ).", {$sourceid} )
  			ON CONFLICT (identifier) DO UPDATE
  				SET datestamp=".$pg->qstr( $datestamp ).", type=".$pg->qstr( 'journal' ).", data=".$pg->qstr( $json ).", oai_pmh_source_id={$sourceid}";
  	//echo "\n------\n{$sql}\n--------\n";
  	$pg->Execute( $sql );

  	$entity->loadFromArray( $record->getIdentifier(), $data, 'doaj' );
      $solr->import( $entity );
  }
}
else {
	$sql = "SELECT * FROM oai_pmh WHERE oai_pmh_source_id={$sourceid}";
	$rs = $pg->Execute( $sql );
	$rows = $rs->RecordCount();
	$i = 1;
	foreach( $rs as $row ) {
		$data = (array)json_decode( $row['data'] );
		$identifier = $row['identifier'];
		echo "{$i}/{$rows}: {$identifier}\n";
		$entity->loadFromArray( $identifier, $data, 'doaj' );
		$solr->import( $entity );
		$i++;
	}
}
?>
