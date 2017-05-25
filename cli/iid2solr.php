<?php

namespace Mediathek;

include '../init.inc.php';

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';

$entity = new IIDWorkEntity( $db );
$solr = new SOLR( $solrclient );

/*
 localhost:8983/solr/base/update?stream.body=<delete><query>source:IIDWork</query></delete>
*/
//$sql = "SELECT DISTINCT `idArbeiten` as id FROM archiv_iid.Arbeiten";
$sql = "SELECT a.idArbeiten AS id
    FROM archiv_iid.Arbeiten a, archiv_iid.StatiProArbeit spa, archiv_iid.Stati s
    WHERE a.idArbeiten=spa.Arbeiten_idArbeiten AND s.idStati=spa.Stati_idStati AND s.idStati=280 AND a.deleted IS NULL";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['id'];

    $entity->loadFromDatabase( $sys );

    $title = $entity->getTitle();
    echo "Title: ".$title."\n";

    $cluster = $entity->getCluster();
    echo "Cluster: ";
    print_r( $cluster );

    $authors = $entity->getAuthors();
    echo "Authors: ";
    print_r( $authors );

    $solr->import( $entity );
}
$rs->Close();
