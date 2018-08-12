<?php

namespace Mediathek;

include '../init.inc.php';

$xmlfile = '/root/DG_OA_eBooks_2018-07-30.xml';

$z = new \XMLReader;
$z->open($xmlfile);

$doc = new \DOMDocument();
$entity = new degruyterEntity( $db );
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


$update = $solrclient->CreateUpdate();
$update->addCommit();
$result = $solrclient->update( $update );
echo "Commit query executed\n";
echo 'Query status: ' . $result->getStatus()."\n";
echo 'Query time: ' . $result->getQueryTime()."\n";

?>
