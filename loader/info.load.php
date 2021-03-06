<?php
include( '../init.inc.php' );

use GuzzleHttp\Client;
use Stormsys\SimpleHal\Clients\GuzzleHalClient;
use Stormsys\SimpleHal\Resource;
use Stormsys\SimpleHal\Uri\GuzzleUriTemplateProcessor;
use Stormsys\SimpleHal\Uri\LeagueUriJoiner;

$client = new GuzzleHalClient(new Client());
$uriTemplateProcessor = new GuzzleUriTemplateProcessor();
$uriJoiner = new LeagueUriJoiner();
$apiRootUrl = 'https://restclient:pmmefrnH4EynPhbvoNEYoeV2@mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages/11';

$pages = new Resource($client, $uriTemplateProcessor, $uriJoiner, $apiRootUrl);

//$pages = $root->follow( 'https://mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages' );

/*
$client = new GuzzleHttp\Client();
$explorer = new HalExplorer\Explorer();
$adapter = new HalExplorer\ClientAdapters\Adapter();

$adapter->setClient($client);
$explorer->setAdapter($adapter)->setBaseUrl("https://mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2");
$response = $explorer->makeRequest( 'get', 'pages/4');
*/
?>
<div id="info" class="info-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-3 col-sm-12">
                <div class="mask" style="background: none;">
                </div>
                <div class="main-heading">
                    <h1>Information</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-9 col-sm-12">

                <!--( B ) What Can I Do -->

                <div class="clearfix">
                    <h2 class="small-heading"> <?php echo $pages->{'title'}->{'rendered'}; ?></h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
                             <?php echo $pages->{'content'}->{'rendered'}; ?>                           
                        </div>
                    </div>
                </div>
<?php
$apiRootUrl = 'https://restclient:pmmefrnH4EynPhbvoNEYoeV2@mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages?parent=11';

$pages = new Resource($client, $uriTemplateProcessor, $uriJoiner, $apiRootUrl);
foreach( $pages as $page ) {
?>	
            <!--( b ) Profile Page Content -->

                <!--( B ) What Can I Do -->

                <div class="clearfix">
                    <h2 class="small-heading"> <?php echo $page->{'title'}->{'rendered'}; ?></h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
                             <?php echo $page->{'content'}->{'rendered'}; ?>                           
                        </div>
                    </div>
                </div>
<?php					
}
?>
				<?php include( 'footer.inc.php' ); ?>
			</div>
	</div>
 </div>
