<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

use GuzzleHttp\Client;
use Stormsys\SimpleHal\Clients\GuzzleHalClient;
use Stormsys\SimpleHal\Resource;
use Stormsys\SimpleHal\Uri\GuzzleUriTemplateProcessor;
use Stormsys\SimpleHal\Uri\LeagueUriJoiner;

$client = new GuzzleHalClient(new Client());
$uriTemplateProcessor = new GuzzleUriTemplateProcessor();
$uriJoiner = new LeagueUriJoiner();
$apiRootUrl = 'https://restclient:pmmefrnH4EynPhbvoNEYoeV2@mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages?parent=200';

$pages = new Resource($client, $uriTemplateProcessor, $uriJoiner, $apiRootUrl);


global $db;
	
echo mediathekheader('static', 'Mediathek - Handapparate', '');
?>
<div class="back-btn"><i class="ion-ios-search"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings-strong': 'ion-log-in'; ?>"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-1 col-sm-12">
                <div class="mask">
                </div>
                <div style="left: 20%;" class="main-heading">
                    <h1>Handapparat</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix full-height">
                    <h2 class="small-heading">Mediathek</h2>

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
foreach( $pages as $page ) {
?>
			<div class="clearfix">
                    <h3 class="small-heading"> <?php echo $page->{'title'}->{'rendered'}; ?></h3>
                    <?php echo $page->{'content'}->{'rendered'}; ?>                           
                </div>
<?php
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
						<p class="copyright">Copyright &copy; 2016
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
//include( 'bgimage.inc.php' );
?>
<script>

function init() {
	initZotpress();
}
</script>   

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>
<script src="js/threex.windowresize.js"></script>
<script src="js/zotpress.mediathek.js"></script>

<?php
echo mediathekfooter();
?>

  
