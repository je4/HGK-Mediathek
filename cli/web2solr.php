<?php

namespace Mediathek;

include '../init.inc.php';
$solrclient = new Solarium\Client($config['solariumdev']);
$solrclient->getPlugin('postbigrequest');

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';


$entity = new WebEntity( $db );
$solr = new SOLR( $solrclient );

/*
 localhost:8983/solr/dev/update?stream.body=<delete><query>source:hgkweb</query></delete>
*/
// irgendwie die aenderungen aus der middleware holen
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
