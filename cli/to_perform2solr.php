<?php

namespace Mediathek;

include '../init.inc.php';

$entity = new to_performEntity();
$solr = new SOLR( $solrclient );

$sql = "SELECT DISTINCT id FROM source_musik_data";
$rs = $db->Execute( $sql );

foreach( $rs as $row ) {
	
	$id = $row['id'];
		
	$sql = "SELECT * FROM source_musik_data WHERE id=".$db->qstr( $row['id'] );
	$rs2 = $db->Execute( $sql );
	$data = array();
	foreach( $rs2 as $row2 ) {
		$data[$row2['propertyname']] = $row2['value'];
	}
	$rs2->Close();
    $entity->loadFromArray( str_replace( '/data.xml', '', str_replace( 'xml/', '', $id )), $data, 'to_perform' );
    
    //$xml = $entity->getXML();
    //echo $xml->saveXML();
    
    $title = $entity->getTitle();
    echo "Title: ".$title."\n";
    
    $solr->import( $entity );

}
?>