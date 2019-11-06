<?php
namespace Mediathek;

//header('Access-Control-Allow-Origin: https://ba14ns21403.fhnw.ch');
//header('Access-Control-Allow-Origin: *');


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
$id = isset( $_REQUEST['id'] ) ? trim( $_REQUEST['id'] ) : null;
$barcode = isset( $_REQUEST['barcode'] ) ? strtolower( trim( $_REQUEST['barcode'] )) : null;
$json = isset( $_REQUEST['json'] );


$cachestr = '';
foreach( $session->getGroups() as $grp ) {
	$cachestr .= $grp;
}
$cachefile = md5($cachestr."_{$id}{$barcode}").".dat";
$fullcachepath = "{$config['cachedir']}/{$cachefile{0}}/{$cachefile{1}}/detail_{$id}{$barcode}_{$cachefile}";

if( !DEBUG && file_exists( $fullcachepath )) {
	$gzipoutput = file_get_contents( $fullcachepath );
	$headers = getallheaders();
	//var_dump( $headers );
	if( array_key_exists('Accept-Encoding', $headers)) {
		if( strpos($headers['Accept-Encoding'], 'gzip' ) !== false ) {
			header('Content-Encoding: gzip');
			header('Content-Length: '.strlen($gzipoutput));
			echo $gzipoutput;
			exit;
		}
	}
	echo gzdecode($gzipoutput);
	exit;
}

ob_start(); // segment 1
$pagestr = '';

$urlparams = array( 'q'=>$q,
	'page'=>$page,
	'pagesize'=>$pagesize,
	);

$qobj = Helper::readQuery( $q );

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
if( $id ) $qstr = 'id:'.$helper->escapePhrase($id);
else if( $barcode ) $qstr = 'signature:'.$helper->escapePhrase( 'barcode:NEBIS:E75:'.$barcode);
else $qstr = "id:none";

$acl_query = '';
foreach( $session->getGroups() as $grp ) {
	if( strlen( $acl_query ) > 0 ) $acl_query .= ' OR';
	$acl_query .= ' acl_meta:'.$helper->escapePhrase($grp);
}
$squery->createFilterQuery('acl_meta')->setQuery($acl_query);

$squery->setQuery( $qstr );

$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
//echo "<!-- ".$qstr." (Documents: {$numResults}) -->\n";
$doc = ( $numResults > 0 ) ? $rs->getDocuments()[0] : null;

if( $json ) {
	header( 'Content-Type: text/json' );

	$data = (array)$doc->getFields();
	echo json_encode( $data );

	exit;
}
//header("Access-Control-Allow-Origin: https://media.campusderkuenste.ch");
$title = 'Mediathek - Detail - '.($doc ? $doc->title : '').' ['.$id.']';
$robots = 'noindex';
foreach( $config['seo'] as $field=>$vals ) {
	if( !is_array($doc[$field]) || count(array_intersect($doc[$field], $vals )) > 0 ) {
		$robots = 'nofollow';
		break;
	}
}
header( "X-Robots-Tag: {$robots}", true );
echo mediathekheader('search', $title, '', [], ['robots'=>$robots]);

$pagestr .= ob_get_contents();
ob_flush();

?>
<div class="back-btn"><i class="ion-ios-search"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings': 'ion-ios-log-in'; ?>"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-1 col-sm-12">
                <div class="mask">
                </div>
                <div style="left: 20%;" class="main-heading">
                    <h1>Detail</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix full-height">

<?php
$pagestr .= ob_get_contents();
ob_flush();

if( $numResults > 0 ) foreach( $rs as $doc ) {
?>
		  <!-- <a href="search.php?q=<?php echo urlencode( $q ); ?>&page=<?php echo $page; ?>&pagesize=<?php echo $pagesize; ?>">back to search</a><br /> -->
<?php
	$class = '\\Mediathek\\'.$doc->source.'Display';

	$output = new $class($doc, $urlparams, $db, null);
	echo $output->getHeading();
	echo $output->detailView();
	echo '<script type="application/ld+json">'."\n".json_encode( $output->getSchema() )."\n</script>\n";

?>
<?php
$pagestr .= ob_get_contents();
ob_flush();

}
else {
?>
	<h2 class="small-heading">Mediathek</h2>

	<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
	<h2>Error: not found</h2>
<?php
	echo $qstr." (Documents: {$numResults})\n";
}
?>
					</div>
				</div>
			</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Enjoy 2017
							<a href="#">Mediathek</a>
						</p>
					</div>

					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="author">
							<a href="http://www.fhnw.ch/hgk" target="_blank">HGK FHNW</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php
$pagestr .= ob_get_contents();
ob_flush();

//include( 'bgimage.inc.php' );
?>
<script>

function init() {
	$('.carousel-shot').carousel({
	  interval: 2000
	});

	$('body').on('click', '.setting-btn', function () {
		<?php if( $session->isLoggedIn()) { ?>
		window.location="settings.php";
		<?php } else { ?>
		window.location="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>";
		<?php } ?>

	});

	$('body').on('click', '.back-btn', function () {
		window.location="search.php?q=<?php echo urlencode( $q ); ?>&page=<?php echo urlencode( $page ); ?>&pagesize=<?php echo urlencode( $pagesize ); ?>";
	});

<?php
	if( isset( $doc )) echo "init{$doc->source}();";
?>

}
</script>


<?php
echo mediathekfooter();
$pagestr .= ob_get_contents();
ob_end_flush();

file_put_contents($fullcachepath, gzencode($pagestr));

?>
