<?php
namespace Mediathek;

include( 'init.inc.php' );
include( 'lib/firebase/SignatureInvalidException.php' );
include( 'lib/firebase/BeforeValidException.php' );
include( 'lib/firebase/ExpiredException.php' );
include( 'lib/firebase/JWT.php' );
include( 'lib/firebase/JWK.php' );
include( 'lib/php-barcode.php' );

use \Firebase\JWT\JWT;

$jwt = $_REQUEST['jwt'];
$key = $config['wallet']['secret'];

?>
<html>
<head>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="initial-scale=1.0">
<link rel="apple-touch-icon" href="/mt_icon.png">
<style>
body {
	text-align: center;
}
</style>
</head>
<body>
<?php

try {
	$decoded = JWT::decode($jwt, $key, array('HS256'));
	$sql = "SELECT p.displayname, c.* FROM wallet.pass p, wallet.card c WHERE p.id=c.pass AND c.serial=".intval($decoded->sub);
	//echo "{$sql}<br />\n";
	$card = $db->getRow( $sql );
	if( $card == null ) {
		die( "cannot find serial {$decoded->sub}" );
	}
	
	$barcodeFile = $config['tmpprefix'].$card['barcode'].'.png';
	$width = 280; // 375;
	$im     = imagecreatetruecolor($width, 35);
	$black  = ImageColorAllocate($im,0x00,0x00,0x00);
	$white  = ImageColorAllocate($im,0xff,0xff,0xff);
	imagefilledrectangle($im, 0, 0, $width, 35, $white);
	$data = \Barcode::gd($im, $black, $width/2, 18, 0, "code39", $card['barcode'], 2, 25);	
	ob_start (); 
	  imagepng($im);
	  $image_data = ob_get_contents (); 
	ob_end_clean (); 
	
?>	
	<h1><?php echo htmlspecialchars($card['displayname']); ?></h1>
	<h2><?php echo htmlspecialchars($card['name']); ?></h2>
	<img style="width: 100%" alt="My Image" src="data:image/png;base64,<?php echo base64_encode ($image_data); ?>" /><br />
	<?php echo htmlspecialchars($card['serial']); ?>
<?php	
	
	//var_dump( $card );
}
catch( \Exception $e ) {
	echo $e->getMessage();
}
?>
</body>
</html>