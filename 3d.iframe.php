<!DOCTYPE html>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'init.inc.php';

$box = null;
if( isset( $_REQUEST['box'] )) $box = trim( strtoupper($_REQUEST['box']));
$box = str_replace( '_', '', $box );


/**
 * (c) Copyright 2015/2016 info-age GmbH
 *
 */

/*
SELECT sys, substring(value, 4, 14 ) FROM `mediathekmarc` WHERE `tag` LIKE '949' AND value LIKE '|0 EM%|1 E75%' 
*/ 
 
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>HGK Mediathek</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="description" content="FHNW HGK Mediathek">
    <meta name="keywords" content="Media, Library, Book, Digital">

		<style>
			body { margin: 0; }
			canvas { width: 100%; height: 100% }
		</style>	

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek_helper.js"></script>   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>   

<script>
var mediathek = null;
var mediathek3D = null;
var hash = null;
var px = 0;
var py = -5;
var pz = 5;
var gridWidth = 500;

function init() {
	
	hash = "<?php echo $box; ?>";
	
	$(".renderer").height( $(window).height());
	$(".renderer").width( $(window).width());
	
	mediathek = new Mediathek( {
		camType: "perspective",
		renderDOM: $(".renderer"),
		backgroundColor: 0x425164,
		camJSON: null, //'[0.9993451833724976,0.015195777639746666,-0.03283816948533058,0,0.01806975156068802,0.5766857266426086,0.8167662024497986,0,0.03134870156645775,-0.816824734210968,0.5760335326194763,0,286.9632568359375,-7477.142578125,5272.96044921875,1]',
	})
	
	var row = hash.substring( 0, 1 );
	

	var drawOnly = null;
	if( hash.length > 1)
		drawOnly = hash.substring( 0, 1 );
	
	mediathek3D = new Mediathek3D( {
			floorImage: 'img/mt_background.png',
//			satImage: 'baselcard.jpg',
			boxData: 'kistendata.load.php',
			bottomColor: 0x283744,
			boxColor: 0xe0e0e0,
			//boxColorHighlight: 0x283744,
			boxDelay: 1,
			drawOnly: drawOnly,
		}, 
		function( object ) {
			mediathek.scene.add(object);
//			object.boxHighlight( "<?php echo $box; ?>", true );
			animate();
			
			object.renderBoxes(0);
			
			mediathek3D.boxHighlight( hash.substring( 0, 4), true );
			if ("onhashchange" in window) {
				$(window).bind( 'hashchange', function(e) { 
					hash = window.location.hash.substring( 1 );
					console.log( "Hash: " + hash);
					mediathek3D.clearHighlight();
					mediathek3D.boxHighlight( hash.substring( 0, 4), true );
				});
			}
	
			mediathek.camera.position.set( px*gridWidth, py*gridWidth, pz*gridWidth );
			mediathek.camera.up = new THREE.Vector3(0,0,1);
			box = mediathek3D.boxes[hash.substring( 0, 4)];
			mediathek.controls.target.copy( box.position );
	
			$(document).keyup( function( event ) {
				console.log( event.which );
				switch ( event.which ) {
                    case 33: // PgUp
						pz++;
						break;
                    case 34: // PgDown
						pz--;
						break;
                    case 39: // ->
						px++;
						break;
                    case 37: // <-
						px--;
						break;
                    case 38: // up
						py++;
						break;
                    case 40: // down
						py--;
						break;
                }
				mediathek.camera.position.set( px*gridWidth, py*gridWidth, pz*gridWidth );
				mediathek.camera.up = new THREE.Vector3(0,0,1);
				box = mediathek3D.boxes[hash.substring(0, 4)];
				mediathek.controls.target = box.position;				
				
			});
		}
	);
		
}


function animate() {
    requestAnimationFrame(function() animate);
    mediathek.render();
}


function onWindowResize() {
	console.log( "resize" );
	if( mediathek == null ) return;
	console.log( "resize2" );
	mediathek.windowResize();

}

</script>
   	
      </head>
      <body class="skin-black">


        <!-- jQuery 2.0.2 -->
        <script src="js/jquery.min.js" type="text/javascript"></script>


		
<script language="javascript">
   $( document ).ready(function() {
       init();

   });
</script>


		<div class="renderer" />
</body>
</html>