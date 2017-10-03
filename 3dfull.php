<!DOCTYPE html>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$box = null;
if( isset( $_REQUEST['box'] )) $box = trim( strtoupper($_REQUEST['box']));

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

  	
      </head>
      <body class="skin-black">



		<a href="#" class="downloadCanvas" download="YourFileName.png">image</a>
		<a href="#" class="downloadCamera" download="YourFileName.json">camera</a>
		<div class="renderer" style="width: 1350px; height: 900px;" />
		
	<textarea >
	</textarea>

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek_helper.js"></script>   
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

	hash = window.location.hash.substring( 1 );
	init3D( hash );	

	$("textarea").keyup(function(e) {
		var code = e.keyCode ? e.keyCode : e.which;
		if (code == 13) {  // Enter keycode
//			alert( $(this).val());
			mediathek.setCamJSON($(this).val());
		}
	 });
	
	$(window).on('hashchange',function(){ 
		hash = window.location.hash.substring( 1 );
		mediathek3D.clearHighlight();
		mediathek3D.boxHighlight( hash.substring( 0, 4), true );
	});
}


function animate() {
    requestAnimationFrame(animate);
    mediathek.render();
}


function onWindowResize() {
	console.log( "resize" );
	if( mediathek == null ) return;
	console.log( "resize2" );
	mediathek.windowResize();

}

</script>
 
        <!-- jQuery 2.0.2 -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script src="js/jquery.min.js" type="text/javascript"></script>

        <!-- jQuery UI 1.10.3 -->
        <script src="js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
        <!-- Bootstrap -->
        <!-- <script src="js/bootstrap.min.js" type="text/javascript"></script> -->
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

		
<script language="javascript">
   $( document ).ready(function() {
       init();
//       $(document.body).dblclick( function (e) {
//   	    console.log(mediathek.getCamJSON());
//   	})
	   
	$('.downloadCanvas').click( function() {
		var canvas = $('.renderer').children( 'canvas' ).first();
		var dt = canvas[0].toDataURL('image/png');
		$('.downloadCanvas').attr( 'href', dt );
		$('.downloadCanvas').attr( 'download',  window.location.hash.substring( 1 ) + ".png" );
		
	});
	$('.downloadCamera').click( function() {
		var canvas = $('.renderer').children( 'canvas' ).first();
		$('.downloadCamera').attr( 'href', URL.createObjectURL(new Blob([mediathek.getCamJSON()], {type: "text/json"}) ));
		$('.downloadCamera').attr( 'download',  window.location.hash.substring( 1 ) + ".json" );
		
	});

   });
</script>

</body>
</html>