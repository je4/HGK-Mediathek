<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

global $db;

// get request values
$func = isset( $_REQUEST['func'] ) ? $_REQUEST['func'] : 'kisten';
	
echo mediathekheader('inventar', 'Mediathek - Inventar', '');
?>
<div class="back-btn"><i class="ion-android-arrow-back"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings-strong': 'ion-log-in'; ?>"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
		<!--( a ) Profile Page Fixed Image Portion -->

		<div class="image-container col-md-1 col-sm-12">
			<div class="mask">
			</div>
			<div style="left: 20%;" class="main-heading">
				<h1>Inventar</h1>
			</div>
		</div>

		<!--( b ) Profile Page Content -->

		<div class="content-container col-md-offset-1 col-md-10 col-sm-12">
			<div class="clearfix full-height">
				<h2 class="small-heading">Inventar</h2>
				<?php include( 'inventar.'.$func.'.php'); ?>
			</div>
		</div>
	</div>
</div>
<script>

function init() {

	$('body').on('click', '.setting-btn', function () {
		<?php if( $session->isLoggedIn()) { ?>
		var modal = $('#MTModal');
		modal.find('.modal-title').text( '<?php echo htmlspecialchars( $session->shibGetUsername()); ?>' );
		var content = <?php 
		echo "'<p>".htmlspecialchars( $session->shibHomeOrganization())."<br />'
			 +'".htmlspecialchars( $session->shibGetMail()) ."<br />'
			 +'<ul>'";
		foreach( $session->getGroups() as $grp ) {
			echo "+'<li>".htmlspecialchars( $grp )."</li>'";
		}
		echo "+'</ul>'";
?>;		
		modal.find( '.modal-body' ).html( content );
		modal.modal('show');
		<?php } else { ?>
		window.location="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>";
		<?php } ?>
		
	});

	$('body').on('click', '.back-btn', function () {
		window.location="index.php";
	});
	
	       $('[data-toggle="popover"]').popover({
		   html: true
	   });
	
}
</script>   
<script src="js/tether.min.js"></script>   

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

  
