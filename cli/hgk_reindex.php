<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$tmpfile = $config['tmpprefix']."ids_hgk.dat";
$cntfile = $config['tmpprefix']."counter_hgk.dat";
$cursorfile = $config['tmpprefix']."cursor_hgk.dat";

$entity = new swissbibEntity( $db );
$solr = new SOLR( $solrclient, $db );
$entities = array();

if( false ) {
do {
	$sql = "SELECT id FROM `enrich_isbn` WHERE 1 GROUP BY id HAVING COUNT(*) = 1 LIMIT 0, 1000";
	$rows = $db->GetAll( $sql );
	foreach( $rows as $row ) {
		$sql = "DELETE FROM enrich_isbn WHERE id=".$db->qstr( $row['id'] );
		echo "{$sql}\n";
		$db->Execute( $sql );
	}
} while ( count( $rows ) >= 1000 );
do {
	$sql = "select id, code from enrich_isbn GROUP BY id,code having count(*) > 1 LIMIT 0, 1000";
	$rows = $db->GetAll( $sql );
	foreach( $rows as $row ) {
		$sql = "DELETE FROM enrich_isbn WHERE type='ISBN' AND id=".$db->qstr( $row['id'] )." AND code=".$db->qstr( $row['code']);
		echo "{$sql}\n";
		$db->Execute( $sql );
	}
} while ( count( $rows ) >= 1000 );

//exit;
}
if(true) {

	$customizer = $solrclient->getPlugin('customizerequest');

	$pagesize = 10000;
	$page = 0;
	$numPages = 1;

	$counter = 1;

	$cursormark = '*';

  //$entity = new swissbibEntity( $db );
	$entities = [];

	do {
		$squery = $solrclient->createSelect();
		$squery->setRows( $pagesize );
		//$squery->createFilterQuery('notdone')->setQuery("-catalog:*");
		$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
		//$squery->setFields( array( 'id' ));

//		$squery->createFilterQuery('source')->setQuery( 'source:"swissbib"' );

//		$squery->createFilterQuery( 'category' )->setQuery( "catalog:openaccess_books" );
		$squery->createFilterQuery( 'category' )->setQuery( "category:2!!signature!!NEBIS!!E75" );
		//$squery->createFilterQuery( 'category' )->setQuery( "category:0!!area AND -category:1!!area!!unknown" );
		//$squery->createFilterQuery( 'category' )->setQuery( "category:2!!signature!!NEBIS!!E44" );
		//$squery->createFilterQuery( 'catalog' )->setQuery( "catalog:FHNWeMedien" );
		//$squery->createFilterQuery( 'catalog' )->setQuery( "catalog:FHNW-Bib" );
		//$squery->createFilterQuery( 'online' )->setQuery( "online:true" );
		//$squery->createFilterQuery( 'code' )->setQuery( "code:EISBN* OR code:ISBN*" );

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
			//echo $doc->id."\n";
      $source = $doc->source;

			if( !array_key_exists( $source, $entities )) {
				$class = 'Mediathek\\'.$source.'Entity';
				$entities[$source] = new $class( $db );
			}
			$entity = $entities[$source];
      if( !strlen( $doc->metagz )) {
        echo "    ERROR!!!\n";
        continue;
      }
      //$data = gzdecode( base64_decode( $doc->metagz ));
      echo "   loading...";
      $entity->loadFromDoc( $doc );
			echo 'ok';
			$entity->getID();
			echo '.';
			$entity->getSignatures();
			echo '.';
      $codes = $entity->getSigCode( 'NEBIS', 'E75' );
			echo '.';
      foreach( $codes as $code=>$sig ) {
        $sql = "REPLACE INTO ARC( Strichcode, Signatur ) VALUES( ".$db->qstr( $code ).", ".$db->qstr( $sig )." )";
        echo "{$code}:{$sig}\n";
        $db->Execute( $sql );
      }
			$solr->import($entity);
			$counter++;
		}
		$data = $rs->getData();

		$update = $solrclient->CreateUpdate();
		$update->addCommit();
		$result = $solrclient->update( $update );
		echo "Commit query executed\n";
		echo 'Query status: ' . $result->getStatus()."\n";
		echo 'Query time: ' . $result->getQueryTime()."\n";

		if( $cursormark == $data["nextCursorMark"] )
			break;

		$cursormark = $data["nextCursorMark"];
		$page++;
	} while( true );

}


doConnectMySQL();
?>
