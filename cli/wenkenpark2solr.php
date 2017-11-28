<?php

/**

http://localhost:8983/solr/base/update?stream.body=%3Cdelete%3E%3Cquery%3Ecatalog%3AWenkenpark%3C%2Fquery%3E%3C%2Fdelete%3E

*/

namespace Mediathek;

include '../init.inc.php';

$data = json_decode( file_get_contents( 'vww.json' ), true );

//echo implode(', ', array_keys( $data ));
//exit;
$entity = new WenkenparkEntity( $db );
$solr = new SOLR( $solrclient );


$sql = "SELECT DISTINCT `Publikationsnummer` as id FROM source_wenkenpark WHERE `Publikationsnummer` IS NOT NULL";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];

    $entity->loadFromDatabase( $sys, 'vww', array_key_exists( $sys, $data ) ? $data[$sys] : null );

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


    //exit;

    $solr->import( $entity );
}
$rs->Close();

$update = $solrclient->CreateUpdate();
$update->addCommit();
$result = $solrclient->update( $update );
echo "Commit query executed\n";
echo 'Query status: ' . $result->getStatus()."\n";
echo 'Query time: ' . $result->getQueryTime()."\n";

?>
