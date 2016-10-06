<?php
namespace Mediathek;

include( 'init.inc.php' );

global $db;

$pagesize = 1000;
$page = 0;
$mapsize = 20000;

$map = isset( $_REQUEST['map'] ) ? intval( $_REQUEST['map'] ) : null;
if( $map === null ) {
	$squery = $solrclient->createSelect();
	$squery->setRows( 5 );
	$squery->setStart( $page * $pagesize );
	$squery->setFields(array('id'));
	$qstr = '*:*';
	$squery->setQuery( $qstr );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	$num = floor( $numResults / (real)$mapsize );
	if( $numResults % $mapsize > 0 ) $num++;
	header( 'Content-type: text/xml' );
?>
<?xml version="1.0" encoding="UTF-8"?>
   <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
	for( $i = 0; $i < $num; $i++ ) {
?>
		<sitemap>
		   <loc>https://mediathek.hgk.fhnw.ch/sljfsdljf.php?map=<?php echo $i; ?></loc>
		</sitemap>
<?php
	}
?>
   </sitemapindex>
<?php
}
else{
	header( 'Content-type: text/plain' );
	do {
		$first = $map * $mapsize + $page * $pagesize;
		$last = $first + $pagesize - 1;
		$squery = $solrclient->createSelect();
		$squery->setRows( $pagesize );
		$squery->setStart( $first );
		$squery->setFields(array('id'));
		$qstr = '*:*';
			
		$squery->setQuery( $qstr );
		
		$rs = $solrclient->select( $squery );
		$numResults = $rs->getNumFound();
		foreach( $rs as $doc ) {
			echo 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode($doc->id)."\n";
		}
		$page++;
	} while( $last < min( $numResults, ($map+1)*$mapsize-1 ));
}


