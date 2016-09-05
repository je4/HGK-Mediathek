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
			body { margin: 0; overflow:hidden; }
			canvas { width: 100%; height: 100vh; }
		</style>	

  	
      </head>
      <body class="skin-black">

	  <div style="width:100%; height:100vh;" class="renderer" />
	  
<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek_helper.js"></script>   
<script src="js/mediathek3d.js"></script>     
<script src="js/threex.windowresize.js"></script>     

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
	
	// $(window).on('resize', onWindowResize );
	

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
	   

   });
</script>

</body>
</html>