<?php

namespace Mediathek;

include '../init.inc.php';


$solr = new SOLR( $solrclient );
$zotero = new \Zotero\Library( 'group', '704562', 'HDU7KZ9M', 'XxuGdxZuXiB1epXH8B9XX2oR' );

$collections = $zotero->fetchCollections(array('collectionKey'=>'', 'content'=>'json'));

//var_dump( $collections );
?>