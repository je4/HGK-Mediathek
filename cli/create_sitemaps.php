<?php
namespace Mediathek;

include( '../init.inc.php' );

global $db;

$pagesize = 1000;
$page = 0;
$mapsize = 20000;

  $mf = fopen( "../lsajfldsf2d.xml", 'w+' );
  fwrite( $mf, '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' );
	$squery = $solrclient->createSelect();
	$squery->setRows( 5 );
	$squery->setStart( $page * $pagesize );
	$squery->setFields(array('id'));
	$squery->createFilterQuery('acl_meta')->setQuery("acl_meta:global/guest");
	$squery->createFilterQuery('source')->setQuery("source:zotero OR source:ikuvid OR source:Wenkenpark OR catalog:HGK");
	$squery->createFilterQuery('not')->setQuery("-(source:eartnet OR source:EZB OR source:DOAJArticle)");
	$qstr = '*:*';
	$squery->setQuery( $qstr );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	$num = floor( $numResults / (real)$mapsize );
	if( $numResults % $mapsize > 0 ) $num++;
	for( $i = 0; $i < $num; $i++ ) {
		$file = "lsajfldsf2d_map{$i}.gz";
    if( file_exists( '../'.$file )) unlink( '../'.$file );
    $gz = gzopen( '../'.$file, 'w9' );
    gzwrite( $gz, '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' );
    $map = $i;
    $page = 0;
    do {
      $first = $map * $mapsize + $page * $pagesize;
      $last = $first + $pagesize - 1;

      echo "Map: {$i} : {$first} - {$last} of {$numResults}\n";
      //echo "Mapsize: {$mapsize}\n";
      //echo "Pagesize: {$pagesize}\n";
      //echo "Page: {$page}\n";
      //echo "First: {$first}\n";
      //echo "Last: {$last}\n";
      $squery = $solrclient->createSelect();
      $squery->setRows( $pagesize );
      $squery->setStart( $first );
      $squery->setFields(['id', 'creation_date']);
      $squery->createFilterQuery('acl_meta')->setQuery("acl_meta:global/guest");
      $squery->createFilterQuery('source')->setQuery("source:zotero OR source:ikuvid OR source:Wenkenpark OR catalog:HGK OR catalog:HGKplus");
      $squery->createFilterQuery('not')->setQuery("-(source:eartnet OR source:EZB OR source:DOAJArticle)");
      $qstr = '*:*';
      $squery->setQuery( $qstr );

      $rs = $solrclient->select( $squery );
      foreach( $rs as $doc ) {
        gzwrite( $gz, "<url><loc>https://mediathek.hgk.fhnw.ch/detail.php?id=".urlencode($doc->id)."</loc><lastmod>{$doc->creation_date}</lastmod></url>" );
      }
      $page++;
    } while( $last < min( $numResults, ($map+1)*$mapsize-1 ));

    gzwrite( $gz, '</urlset>' );
    gzclose( $gz );
    fwrite( $mf, "<sitemap><loc>https://mediathek.hgk.fhnw.ch/{$file}</loc></sitemap>" );
	}
  fwrite( $mf, '</sitemapindex>' );
  fclose( $mf );
