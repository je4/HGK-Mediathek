<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

global $db;

// get request values
$query = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
$q = isset( $_REQUEST['q'] ) ? strtolower( trim( $_REQUEST['q'] )): null;
$page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 0;
$pagesize = isset( $_REQUEST['pagesize'] ) ? intval( $_REQUEST['pagesize'] ) : 25;
$id = isset( $_REQUEST['id'] ) ? strtolower( trim( $_REQUEST['id'] )) : null;

echo mediathekheader('detail', null);
?>
	<div class="container-fluid" style="margin-top: 0px; padding: 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
<?php

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();


?>
		</div>
	   </div>
<?php
$qstr = 'id:'.$id;
$squery->setQuery( $qstr );

$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
echo "<!-- ".$qstr." (Documents: {$numResults}) -->\n";
if( $numResults > 0 ) foreach( $rs as $doc ) {
?>
	   <div class="row">
		  <div class="col-md-9">
		  <a href="search.php?q=<?php echo urlencode( $q ); ?>&page=<?php echo $page; ?>&pagesize=<?php echo $pagesize; ?>">back to search</a><br />
<?php
	$class = '\\Mediathek\\'.$doc->source.'Display';

	$output = new $class($doc, null, $db);
	$html = $output->detailView();
	echo $html;

?>
		  </div>
   		  <div class="col-md-3" style="background-color: transparent;">
		  </div>

	   </div>
<?php
}
?>
	</div>

	
<?php
include( 'bgimage.inc.php' );
?>
<script>

function init() {
$('.carousel-shot').carousel({
  interval: 2000
})
}
</script>   
<?php
echo mediathekfooter();
?>

  
