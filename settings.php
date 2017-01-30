<?php
namespace Mediathek;

use \Passbook\Pass\Field;
use \Passbook\Pass\Location;
use \Passbook\Pass\Image;
use \Passbook\PassFactory;
use \Passbook\Pass\Barcode;
use \Passbook\Pass\Structure;
use \Passbook\Type\StoreCard;


require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

echo mediathekheader('search', 'Mediathek - Settings', '');
?>
<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-1 col-sm-12">
                <div class="mask">
                </div>
                <div style="left: 20%;" class="main-heading">
                    <h1>Einstellungen</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix full-height">
                    <h2 class="small-heading">Mediathek</h2>
					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php if( $session->isLoggedIn()) { ?>

<h4><a href="<?php echo $_SERVER['Meta-organizationURL']; ?>"><?php 
	if( isset( $_SERVER['Meta-largeLogo'])) echo $_SERVER['Meta-largeLogo']; 
	echo "&nbsp;&nbsp;".$_SERVER['Meta-displayName'];
?></a></h4>
<h3><?php echo htmlspecialchars( $session->shibGetUsername()); ?></h3>
<h4><a href="mailto:<?php echo $session->shibGetMail(); ?>"><?php echo $session->shibGetMail(); ?></a></h4>
<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12 price-catagory full-height">
		<div class="price-box">
			<p class="pricing-catagory-name">Elektronischer Bibliotheksausweis</p>
			<p><a href="ausweis.php">Verwalten & Beantragen</a></span></p>
		</div>
	</div>
	<div class="col-md-6 col-sm-6 col-xs-12 price-catagory">
		<div class="price-box">
			<p class="pricing-catagory-name">Sicherheitsgruppen</p>
			<ul>
<?php			
	foreach( $session->getGroups() as $grp )
		echo "			<li>".htmlspecialchars( $grp )."</li>\n";
?>				
			</ul>
		</div>
	</div>
<!--	
	<div class="col-md-6 col-sm-6 col-xs-12 price-catagory">
		<div class="price-box">
			<p class="pricing-catagory-name">Business</p>
			<p><span class="price">$29</span>/Month</p>
			<ul>
				<li>15 Projects</li>
				<li>30 GB Storage</li>
				<li>Unlimited Data Transfer</li>
				<li class="unavailabe">50 GB Bandwith</li>
				<li class="unavailabe">Enhanced Security</li>
			</ul>

			<a href="#" class="btn">learn more</a>
		</div>
	</div>
-->	
</div>
<pre>
<!-- <?php nl2br (htmlspecialchars( print_r( $_SERVER ))); ?> -->
</pre>

<?php  } else { ?>
Bitte <a href="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>">anmelden</a>, um einen digitalen NEBIS-Ausweis zu erstellen oder die persönlichen Einstellungen anzupassen.
<?php } ?>
					
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
	$('.carousel-shot').carousel({
	  interval: 2000
	});

	$('body').on('click', '.back-btn', function () {
		window.location="search.php";
	});
	
}
</script>   

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>

<?php
echo mediathekfooter();
?>

  
