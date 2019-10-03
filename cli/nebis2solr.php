<?php

namespace Mediathek;

include '../init.inc.php';

$chars = '0123456789abcdef';

$dir = $config['cachedir'];

for( $i = 0; $i < strlen($chars); $i++ ) {
	$h = "{$dir}/{$chars{$i}}";
	echo "{$h}\n";
	mkdir($h);
	for( $j = 0; $j < strlen($chars); $j++ ) {
		$h = "{$dir}/{$chars{$i}}/{$chars{$j}}";
		echo "{$h}\n";
		mkdir($h);
	}
}

exit;



$entity = new MarcEntity( $db );
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


$sql = "SELECT DISTINCT sys FROM nebis_grab";
$rs = $db->Execute( $sql );
$ntcounter = 0;
foreach( $rs as $row ) {
    $sys = $row['sys'];

    $entity->loadFromDatabase( $db, $sys, 'nebis' );
    
    //$xml = $entity->getXML();
    //echo $xml->saveXML();
    
    $title = $entity->getTitle();
    if( !strlen( $title )) {
        echo (++$ntcounter).": no title\n";
		echo $entity->getID()."\n";
        continue;
    }
//	else continue;
    echo "Title: ".$title."\n";
/*
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
*/    
    $solr->import( $entity );

}
$rs->Close();
?>