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
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings-strong': 'ion-log-in'; ?>"></i></div>

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
                    <h2 class="small-heading">Mediathek der Künste</h2>
					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php if( $session->isLoggedIn()) { ?>

<h4><a href="<?php echo $_SERVER['Meta-organizationURL']; ?>"><?php 
	if( isset( $_SERVER['Meta-largeLogo'])) echo $_SERVER['Meta-largeLogo']; 
	echo "&nbsp;&nbsp;".$_SERVER['Meta-displayName'];
?></a></h4>
<h3><?php echo htmlspecialchars( $session->shibGetUsername()); ?></h3>
<h4><a href="mailto:<?php echo $session->shibGetMail(); ?>"><?php echo $session->shibGetMail(); ?></a></h4>
<div class="row">
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
	<div class="col-md-6 col-sm-6 col-xs-12 price-catagory">
		<div class="price-box">
			<p class="pricing-catagory-name">Elektronischer Bibliotheksausweis</p>
<?php
	$sql = "SELECT *, (expiryDate > DATE_ADD( NOW(), INTERVAL 3 MONTH )) AS ok FROM card WHERE email LIKE ".$db->qstr( $session->shibGetMail());
	$row = $db->getRow( $sql );
	if( $row ) {
		if( !strlen( $row['uniqueID'] )) {
			$sql = "UPDATE card SET uniqueID=".$db->qstr( $session->shibGetUniqueID())."
						, givenName=".$db->qstr( $session->shibGetGivenName())."
						, surname=".$db->qstr( $session->shibGetSurname())." 
					WHERE email LIKE ".$db->qstr( $session->shibGetMail());
			$db->Execute( $sql );
		}
		$walletfile = $config['wallet']['dir'].'/'.$row['cardid'].'.pkpass';
		if( !file_exists( $walletfile ) || $row['ok'] != 1 ) {
			// Create an Store ticket
			$pass = new StoreCard($row['cardid'], "Benutzerausweis Mediathek HGK");
			$pass->setBackgroundColor('rgb(255, 255, 255)');
			$pass->setLogoText('Mediathek HGK');

			$location = new Location( 47.533305, 7.611023 );
			$location->setAltitude( 46 );
			$pass->addLocation( $location );

			$expirationDate = (new \DateTime())->add( new \DateInterval('P6M') );
			$pass->setExpirationDate( $expirationDate );

			// Create pass structure
			$structure = new Structure();

			// Add primary field
			$primary = new Field('Title', 'NEBIS Benutzerausweis');
			$structure->addPrimaryField($primary);

			// Add secondary field
			$secondary = new Field('Name', "{$row['givenName']} {$row['surname']}");
			$secondary->setLabel( 'Name' );
			$structure->addSecondaryField($secondary);

			$secondary2 = new Field('Cardnumber', $row['libraryid']);
			$secondary2->setLabel( 'Kartennummer' );
			$structure->addSecondaryField($secondary2);

			// Add back field
			$back = new Field( 'Library', 'Mediathek HGK' );
			$back->setLabel( 'Bibliothek' );
			$structure->addBackField( $back );
			$back1 = new Field( 'Name', "{$row['givenName']} {$row['surname']}" );
			$back1->setLabel( 'Name' );
			$structure->addBackField( $back1 );
			$back2 = new Field( 'Cardnumber', $row['libraryid'] );
			$back2->setLabel( 'Kartennummer' );
			$structure->addBackField( $back2 );
			$back3 = new Field( 'Email', $row['email'] );
			$back3->setLabel( 'Emailadresse' );
			$structure->addBackField( $back3 );
			$back4 = new Field( 'Valid', $expirationDate->format( 'd.m.Y H:i:s' ) );
			$back4->setLabel( 'Gültig bis' );
			$structure->addBackField( $back4 );
			$back2 = new Field( 'Serial', $row['cardid'] );
			$back2->setLabel( 'Seriennummer' );
			$structure->addBackField( $back2 );

			// Set pass structure
			$pass->setStructure($structure);

			// Add icon image
			$icon = new Image($config['wallet']['dir'].'/'.'nwallet_icon.png', 'icon');
			$pass->addImage($icon);

			// Add logo image
			$logo = new Image($config['wallet']['dir'].'/'.'nwallet_logo.png', 'logo');
			$pass->addImage($logo);

			// Add strip image
			$strip = new Image($config['wallet']['dir'].'/'.'nwallet_strip.png', 'strip');
			$pass->addImage($strip);

			// Add barcode
			$barcode = new Barcode(Barcode::TYPE_CODE_128, $row['libraryid'] );
			$pass->setBarcode($barcode);

			// Create pass factory instance
			$factory = new PassFactory($config['wallet']['passid']
									, $config['wallet']['passteamid']
									, $config['wallet']['passteamname']
									, $config['wallet']['passcert']
									, $config['wallet']['passpwd']
									, $config['wallet']['applecert']);
			$factory->setOutputPath( $config['wallet']['dir'] );
			$factory->package($pass);
			
			$sql = "UPDATE card SET issuedate=NOW(), expirydate=".$db->qstr( $expirationDate->format( 'Y-m-d H:i:s' ) )." WHERE cardid=".$db->qstr( $row['cardid'] );
			$db->Execute( $sql );
		}
		$sql = "SELECT * FROM card WHERE email LIKE ".$db->qstr( $session->shibGetMail());
		$row = $db->getRow( $sql );
?>
			<p><?php echo "{$row['givenName']} {$row['surname']}"; ?></span></p>
			<ul>
				<li>Seriennummer <?php echo $row['cardid']; ?></li>
				<li>Kartennummer <?php echo $row['libraryid']; ?></li>
				<li>Ausstellungsdatum <?php echo (new \DateTime( $row['issuedate'] ))->format( 'd.m.Y H:i:s' ); ?></li>
				<li>Ablaufdatum <?php echo (new \DateTime( $row['expirydate'] ))->format( 'd.m.Y H:i:s' ); ?></li>
			</ul>
			<p>&nbsp;</p>
			<p><a href="getpass.php"><img width="190px" src="img/Add_to_Apple_Wallet_rgb_DE.svg"></a></p>
<?php		
	} 
	else {
?>
			<p>Zur Aktivierung des digitalen Bibliotheksausweises wenden Sie sich bitte an das Mediatheksteam.</p>
<?php		
	}
?>			
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
<?php nl2br (htmlspecialchars( print_r( $_SERVER ))); ?>
</pre>

<?php  } else { ?>
Bitte <a href="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>">anmelden</a>, um die persönlichen Einstellungen anzupassen.
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
							<a href="#">Mediathek der Künste</a>
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

  
