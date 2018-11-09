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
	$squery->createFilterQuery('acl_meta')->setQuery("acl_meta:global/guest");
	$squery->createFilterQuery('source')->setQuery("source:zotero OR source:ikuvid OR source:Wenkenpark OR catalog:HGK");
	$squery->createFilterQuery('not')->setQuery("-(source:eartnet OR source:EZB OR source:DOAJArticle)");
	$qstr = '*:*';
	$squery->setQuery( $qstr );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	$num = ceil( $numResults / (real)$mapsize );
	if( $numResults % $mapsize > 0 ) $num++;
	header( 'Content-type: text/xml' );
?>
<?xml version="1.0" encoding="UTF-8"?>
   <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
	for( $i = 0; $i < $num; $i++ ) {
		$file = "sljfsdljfq_map{$i}.gz";
?>
		<sitemap>
		   <loc>https://mediathek.hgk.fhnw.ch/<?php echo $file; ?>/loc>
		</sitemap>
<?php
	}
?>
   </sitemapindex>
<?php
}
else{
	header( 'Content-type: text/xml' );
	?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php
	do {
		$first = $map * $mapsize + $page * $pagesize;
		$last = $first + $pagesize - 1;
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
		$numResults = $rs->getNumFound();
		foreach( $rs as $doc ) {
			echo "<url>\n";
			echo '   <loc>https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode($doc->id)."</loc>\n";
			echo "   <lastmod>{$doc->creation_date}</lastmod>\n";
			echo "</url>\n";
		}
		$page++;
	} while( $last < min( $numResults, ($map+1)*$mapsize-1 ));
?>
</urlset>
<?php
}
