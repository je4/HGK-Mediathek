<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$tmpfile = $config['tmpprefix']."ids_hgk.dat";
$cntfile = $config['tmpprefix']."counter_hgk.dat";
$cursorfile = $config['tmpprefix']."cursor_hgk.dat";

$entity = new swissbibEntity( $db );
$solr = new SOLR( $solrclient );
$entities = array();


if(true) {

	$customizer = $solrclient->getPlugin('customizerequest');

	$pagesize = 10000;
	$page = 0;
	$numPages = 1;

	$counter = 1;

	$cursormark = '*';

  $entity = new swissbibEntity( $db );

  // {"query":"author:\"Ganz, Iris\"","area":"","filter":[],"facets":{"catalog":["HGK","PCB_Basel"]}}
  $s = array();
  $s['area'] = '';
  $s['filter'] = array();
  $s['facets'] = array( 'catalog'=>array( 'PCB_Basel' ));

	do {
		$squery = $solrclient->createSelect();
		$squery->setRows( $pagesize );
		//$squery->createFilterQuery('notdone')->setQuery("-catalog:*");
		$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
		//$squery->setFields( array( 'id' ));
		$squery->createFilterQuery('catalog')->setQuery( 'catalog:"PCB_Basel"' );

		$squery->addSort('id', $squery::SORT_DESC);
		$customizer->createCustomization( 'cursorMark' )
			->setType( 'param' )
			->setName( 'cursorMark' )
			->setValue( $cursormark );
		$rs = $solrclient->select( $squery );
		$numResults = $rs->getNumFound();
		$numPages = floor( $numResults / $pagesize );
		foreach( $rs as $doc ) {
			//echo sprintf( "%0.2f - %08d ", $counter*100/$numResults, $counter ).$doc->id."\n";
			//echo $doc->id."\n";
      $source = $doc->source;
      if( !strlen( $doc->metagz )) {
        echo "    ERROR!!!\n";
        continue;
      }

      $data = gzdecode( base64_decode( $doc->metagz ));
      foreach( $doc->author as $a ) {
        $s['query'] = "author:\"{$a}\"";
        echo "<!-- {$a} --><a href=\"search.php?query=".urlencode(json_encode( $s ))."\">".htmlspecialchars( $a )."</a><br />\n";
      }
			$counter++;
		}
		$data = $rs->getData();
		if( $cursormark == $data["nextCursorMark"] )
			break;

		$cursormark = $data["nextCursorMark"];
		$page++;
	} while( true );

}


doConnectMySQL();
?>
