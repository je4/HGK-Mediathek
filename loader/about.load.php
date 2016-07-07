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
$apiRootUrl = 'https://restclient:pmmefrnH4EynPhbvoNEYoeV2@mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages/4';

$page = new Resource($client, $uriTemplateProcessor, $uriJoiner, $apiRootUrl);

?>
<div id="about" class="about-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-3 col-sm-12">
                <div class="mask">
                </div>
                <div class="main-heading">
                    <h1>Über uns</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-9 col-sm-12">

                <!--( B ) What Can I Do -->

                <div class="clearfix">
                    <h2 class="small-heading"> <?php echo $page->{'title'}->{'rendered'}; ?></h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-12">
                             <?php echo $page->{'content'}->{'rendered'}; ?>                           
                        </div>
                    </div>
                </div>
				
                <div class="clearfix">
					<div class="row">
						<div class="col-md-4 col-sm-12">
							<div class="faq-desc-item">
								<div class="flip-container text-center" ontouchstart="this.classList.toggle('hover');">
									<div class="flipper">
										<div class="front">
											<i class="fa fa-mobile"></i>
											<h5>Ausleihe</h5>
										</div>
										<div class="back">
											<h5>Ausleihe</h5>
											<p>Der ausleihbare Buch- und Medienbestand wird vor Ort und über den <a href="http://www.nebis.ch">NEBIS</a> verliehen.</p>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-12">
							<div class="faq-desc-item">
								<div class="flip-container text-center" ontouchstart="this.classList.toggle('hover');">
									<div class="flipper">
										<div class="front">
											<i class="fa fa-mobile"></i>
											<h5>Sammlungen</h5>
										</div>
										<div class="back">
											<h5>Spezialsammlungen</h5>
											<p>
Videosammlung Institut Kunst<br />
Archiv Printpublikationen Birkhäuser Verlag<br />
DVD-Sammlung Matthias Zehnder<br />
Selektion Handbibliothek G. Ch. Tholen
</p>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-12">
							<div class="faq-desc-item">
								<div class="flip-container text-center" ontouchstart="this.classList.toggle('hover');">
									<div class="flipper">
										<div class="front">
											<i class="fa fa-mobile"></i>
											<h5>Support</h5>
										</div>
										<div class="back">
											<h5>Support</h5>
											<p>Wir unterstützen gern in Fragen zur Recherche, zum wissenschaftlichen Arbeiten oder dem Aufbau von Informationskompetenz.</p>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
<?php				

$apiRootUrl = 'https://restclient:pmmefrnH4EynPhbvoNEYoeV2@mediathek.hgk.fhnw.ch/wordpress/wp-json/wp/v2/pages?parent=4';

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
