<?php

namespace Mediathek;

include '../init.inc.php';

//$xmlfile = 'Springer_MARC_20170201_095130.xml';
$xmlfile = 'Springer_MARC_20170210_194445.xml';
$z = new \XMLReader;
$z->open($xmlfile);

$doc = new \DOMDocument();
$entity = new springerEntity( $db );
$solr = new SOLR( $solrclient, $db );

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
