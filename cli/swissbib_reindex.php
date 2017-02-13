<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$tmpfile = $config['tmpprefix']."ids.dat";
$cntfile = $config['tmpprefix']."counter.dat";
$cursorfile = $config['tmpprefix']."cursor.dat";

$entity = new swissbibEntity( $db );
$solr = new SOLR( $solrclient );
$entities = array();


if( !file_exists( $tmpfile )) {
	$tmp = fopen( $tmpfile, 'w' );

	$customizer = $solrclient->getPlugin('customizerequest');

	$pagesize = 10000;
	$page = 0;
	$numPages = 1;

	$counter = 1;

	$cursormark = '*';

	do {
		$squery = $solrclient->createSelect();
		$squery->setRows( $pagesize );
		//$squery->createFilterQuery('notdone')->setQuery("-catalog:*");
		$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
		$squery->setFields( array( 'id' ));
		$squery->createFilterQuery('category')->setQuery( 'category:"1!!signature!!NATIONALLICENCE" OR category:"1!!signature!!RETROS" OR (category:"2!!signature!!NEBIS!!E01" AND online:true)' );
		//$squery->createFilterQuery('category')->setQuery( 'category:"2!!signature!!NEBIS!!E65" AND online:true' );
		
		$squery->createFilterQuery( "*:*" );
		$squery->addSort('id', $squery::SORT_DESC);
		$customizer->createCustomization( 'cursorMark' )
			->setType( 'param' )
			->setName( 'cursorMark' )
			->setValue( $cursormark );
		$rs = $solrclient->select( $squery );
		$numResults = $rs->getNumFound();
		$numPages = floor( $numResults / $pagesize );
		foreach( $rs as $doc ) {
			echo sprintf( "%0.2f - %08d ", $counter*100/$numResults, $counter ).$doc->id."\n";
			fwrite( $tmp, $doc->id."\n" );
			$counter++;
		}
		$data = $rs->getData();
		if( $cursormark == $data["nextCursorMark"] )
			break;

		$cursormark = $data["nextCursorMark"];
		$page++;
	} while( true );

	fclose( $tmp );
}

$counter = 0;
$start = intval( file_get_contents( $cntfile ));
$tmp = fopen( $tmpfile, 'r' );
while( $id = fgets( $tmp ) ) {
	echo $id;
	if( $start >= $counter ) {
		echo "{$start} >= {$counter}: skip\n";
		$counter++;
		continue;
	}
	echo "\n";
	$query = $solrclient->createSelect();
	$helper = $query->getHelper();
	$query->setQuery( 'id:'.$helper->escapeTerm( $id ));
	$resultset = $solrclient->select( $query );
	if( $resultset->getNumFound() == 1 ) {
		foreach( $resultset->getDocuments() as $doc ) {
			$source = $doc->source;
			$data = gzdecode( base64_decode( $doc->metagz ));
			if( !array_key_exists( $source, $entities )) {
				$class = '\\Mediathek\\'.$source.'Entity';
				echo "creating class {$class}\n";
				$entities[$source] = new $class( $db );
			}
			$entity = $entities[$source];
			echo "   loading...";
			$entity->loadFromDoc( $doc );
			echo "   importing...\n";
			$solr->import( $entity, ($counter % 5000 == 0) );
			echo sprintf( "%08u - ", $counter ).$id."\n";
			if($counter % 1000 == 0) {
				file_put_contents( $cntfile, $counter );
			}
		}
	}
	$counter++;
}
fclose( $tmp );

doConnectMySQL();
?>
