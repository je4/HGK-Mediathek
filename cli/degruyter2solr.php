<?php

namespace Mediathek;

include '../init.inc.php';

$xmlfile = 'DG_complete_MARC_201702.xml';

$z = new \XMLReader;
$z->open($xmlfile);

$doc = new \DOMDocument();
$entity = new degruyterEntity( $db );
$solr = new SOLR( $solrclient );

// move to the first <product /> node
while ($z->read() && $z->name !== 'record');
while ($z->name === 'record')
{
	$data = array();
	
	// either one should work
	//$node = new SimpleXMLElement($z->readOuterXML());
	$node = $doc->importNode($z->expand(), true);

	$xml = '<collection xmlns="http://www.loc.gov/MARC21/slim">'.$doc->saveXML( $node ).'</collection>';
	$doc->loadXML( $xml );
	
	$entity->loadNode( null, $doc, '' );
	echo $entity->getID().' - '.$entity->getTitle()."\n";
	
	$solr->import( $entity, false );
	
	// go to next <product />
	$z->next('record');
}


//$entity = new degruyterEntity( $db );
$solr = new SOLR( $solrclient );

?>