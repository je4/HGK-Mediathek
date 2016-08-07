<?php
$doInc = count(get_included_files()) == 1;
if ($doInc) {
	include_once( '../init.inc.php' );
	require_once( '../searchbar.inc.php' );
}


use GuzzleHttp\Client;
use Stormsys\SimpleHal\Clients\GuzzleHalClient;
use Stormsys\SimpleHal\Resource;
use Stormsys\SimpleHal\Uri\GuzzleUriTemplateProcessor;
use Stormsys\SimpleHal\Uri\LeagueUriJoiner;

$client = new GuzzleHalClient(new Client());
$uriTemplateProcessor = new GuzzleUriTemplateProcessor();
$uriJoiner = new LeagueUriJoiner();
$apiRootUrl = 'https://restclient:pmmefrnH4EynPhbvoNEYoeV2@mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages/58';

$page = new Resource($client, $uriTemplateProcessor, $uriJoiner, $apiRootUrl);

?>

<div id="search" class="search-page container-fluid page">


    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-3 col-sm-12">
                <div class="mask">
                </div>
                <div class="main-heading">
                    <h1>Recherche</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-9 col-sm-12">
                <div class="clearfix">
                    <h2 class="small-heading"> <?php echo $page->{'title'}->{'rendered'}; ?></h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
                             <?php echo $page->{'content'}->{'rendered'}; ?>                           
                        </div>
                    </div>
                </div>

                <div class="clearfix">
                   <h2 class="small-heading">Mediathek der Künste</h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
							<p><b>Mediensuche</b></p>
							<?php echo searchbar('', 'all' ); ?><br />
							<p><b>Kistensuche</b></p>
							<input name="kiste" type="text" class="form-control" id="kiste" required="required" placeholder="Signatur eingeben">
							<script>
						   </script>
							<div id="result"></div>
                        </div>
                    </div>
                </div>
                <div class="clearfix">
                   <h2 class="small-heading">Zeitschriften</h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
                             Suchschlitz
                        </div>
                    </div>
                </div>
                <div class="clearfix">
                   <h2 class="small-heading">Datenbanken</h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
                             Suchschlitz
                        </div>
                    </div>
                </div>
                <div class="clearfix">
                   <h2 class="small-heading">Internet</h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
							<div class="form-group">
								<script>
								</script>
								
								
                            </div>
						</div>
                    </div>
                </div>
<?php				

$apiRootUrl = 'https://mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages?parent=58';

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

							<?php include( ($doInc ? '':'loader/').'footer.inc.php' ); ?>
			</div>
	</div>
 </div>
 <script>
 </script>
