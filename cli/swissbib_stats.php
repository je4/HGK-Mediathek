<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$tmpfile = "/data/tmp/ids.dat";
$cntfile = "/data/tmp/counter.dat";

$libs = array();

$entity = new swissbibEntity( $db );
$solr = new SOLR( $solrclient );

function findName( $name ) {
	global $db, $solrclient, $entity;
	
	$parts = explode( '!!', $name );
	$code = strtolower( $parts[3] );

	$squery = $solrclient->createSelect();
	$helper = $squery->getHelper();
	$squery->setRows( 1000 );
	echo "category:".$helper->escapeTerm( $name )."\n";
	$squery->createFilterQuery('category')->setQuery("category:".$helper->escapeTerm( $name ));
	$squery->createFilterQuery('swissbib')->setQuery("source:swissbib");
	$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
	$squery->setQuery( "*:*" );
	$rs = $solrclient->select( $squery );
	$fullname = null;
	foreach( $rs->getDocuments() as $doc ) {
		$data = gzdecode( base64_decode( $doc->metagz ));
		$record = new OAIPMHRecord( $data );
		$entity->loadNode( $doc->originalid, $record, 'swissbib' );
		$ns = $entity->getLibNames();
		if( count( $ns ) == null ) continue;
		$names = array();
		foreach( $ns as $key=>$val ) {
			$names[strtolower( $key )] = $val;
		}

		print_r( $names );
		$fullname = array_key_exists( $code, $names ) ? $names[$code] : null;
		if( $fullname != null ) break;
	}
	$sql = "UPDATE stats_swissbib_libs SET fullname=".$db->qstr( $fullname )." WHERE name=".$db->qstr( $name );
	echo "{$sql}\n";
	$db->Execute( $sql );
}

/*
$sql = "SELECT name FROM stats_swissbib_libs";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
	$name = $row['name'];
	echo "{$name}\n";
	if( preg_match( "/2!!signature!!(.+)!!(.+)$/", $name, $matches )) {		
		$sql = "UPDATE stats_swissbib_libs SET network=".$db->qstr( $matches[1] ).", libcode=".$db->qstr( $matches[2] )." WHERE name=".$db->qstr( $name );
		echo "{$sql}\n";
		$db->Execute( $sql );
	}
}
*/

//$sql = "DELETE FROM stats_swissbib_libs";
$sql = "UPDATE stats_swissbib_libs SET items=-1";
echo "{$sql}\n";
$db->Execute( $sql );

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( 10 );
$squery->createFilterQuery('swissbib')->setQuery("source:swissbib");
$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
$squery->createFilterQuery('new')->setQuery("year:[".(intval(date('Y'))-4)." TO *]");
$squery->setQuery( "*:*" );
//$squery->addSort('id', $squery::SORT_DESC);
$facetSetCatalog = $squery->getFacetSet();
$facetSetCatalog->createFacetField('category')->setField('category')->setPrefix('2!!signature!!')->setLimit( 2000 );

$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
$facetCategory = $rs->getFacetSet()->getFacet('category');
$items_new = array();
foreach( $facetCategory as $value => $count ) {
	$items_new[$value] = $count;
}

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( 10 );
$squery->createFilterQuery('swissbib')->setQuery("source:swissbib");
$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
$squery->setQuery( "*:*" );
//$squery->addSort('id', $squery::SORT_DESC);
$facetSetCatalog = $squery->getFacetSet();
$facetSetCatalog->createFacetField('category')->setField('category')->setPrefix('2!!signature!!')->setLimit( 2000 );

$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
$facetCategory = $rs->getFacetSet()->getFacet('category');
foreach( $facetCategory as $value => $count ) {
	$sql = "UPDATE stats_swissbib_libs SET items={$count}, items_new={$items_new[$value]} WHERE name=".$db->qstr( $value );
	echo "{$sql}\n";
	$db->Execute( $sql );
	if( $db->Affected_Rows() == 0 ) {
		if( preg_match( "/2!!signature!!(.+)!!(.+)$/", $value, $matches )) {
			$sql = "INSERT INTO stats_swissbib_libs VALUES(".$db->qstr( $value ).", ".$db->qstr( $matches[1] ).", ".$db->qstr( $matches[2] ).", NULL, {$count}, ".$items_new[$value].")";
			echo "{$sql}\n";
			$db->Execute( $sql );
			findName( $value );
		}
	}
}


$sql = "DELETE FROM stats_swissbib_types WHERE items > 0";
echo "{$sql}\n";
$db->Execute( $sql );
foreach( array( true, false ) as $new ) {
	$sql = "SELECT name, network, libcode FROM stats_swissbib_libs";
	echo "{$sql}\n";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		foreach( array( true, false ) as $online ) {
			$squery = $solrclient->createSelect();
			$helper = $squery->getHelper();
			$squery->setRows( 10 );
			$q = "category:".$helper->escapeTerm( $row['name'] )
					." AND source:swissbib AND deleted:false AND online:".($online?'true':'false');
			if( $new ) $q .= " AND year:[".(intval(date('Y'))-4)." TO *]";
			//else $q .= " AND year:[* TO ".(intval(date('Y'))-7)."]";
			echo "{$q}\n";
			$squery->createFilterQuery('q')->setQuery($q);
			$squery->setQuery( "*:*" );
			$rs = $solrclient->select( $squery );
			$numResults = $rs->getNumFound();
			if( !$new ) {
				$sql = "SELECT items from stats_swissbib_types" 
						." WHERE network=".$db->qstr($row['network'])
							." AND libcode=".$db->qstr( $row['libcode'] )
							." AND new=true AND online=".($online?'true':'false');
				echo "{$sql}\n";
				$numResults -= intval( $db->GetOne( $sql ));
			}
			if( $numResults == 0 ) continue;
			$sql = "INSERT INTO stats_swissbib_types VALUES(".$db->qstr( $row['network'] )
					.", ".$db->qstr( $row['libcode'] )
					.", ".($new ? 'true':'false')
					.", ".($online?'true':'false')
					.", {$numResults})";
			echo "{$sql}\n";
			$db->Execute( $sql );
		}		
	}
}
doConnectMySQL();
?>
