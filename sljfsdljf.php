<?php
namespace Mediathek;

header( 'Content-type: text/plain' );

include( 'init.inc.php' );

global $db;

$pagesize = 1000;
$page = 0;

do {
	$squery = $solrclient->createSelect();
	$helper = $squery->getHelper();
	$squery->setRows( $pagesize );
	$squery->setStart( $page * $pagesize );
	$squery->setFields(array('id'));
	$qstr = '*:*';
		
	$squery->setQuery( $qstr );
	
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	foreach( $rs as $doc ) {
		echo 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode($doc->id)."\n";
	}
	$page++;
} while( $page* $pagesize < $numResults );
