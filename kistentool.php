<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

global $db;

echo mediathekheader('kiste', null);
?>
	<div class="container-fluid" style="margin-top: 0px; background-color: rgba(255, 255, 255, 0.5); padding: 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-12">
			<div class="jumbotron jumbotron-fluid" style="background: url('img/mt_3d.png') no-repeat center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			background-size: cover;
			-o-background-size: cover;">
				<div class="container" style="background-color: rgba(255, 255, 255, 0.9);">
					<h1>Medien finden</h1>
					<p>Mit Hilfe dieses Werkzeuges können Medien in dem Räumlichkeiten der Mediathek der Künste lokalisiert werden. Einfach eine Signatur eintippen, dann wird die zugehörige Kiste angezeigt. 
					Alternativ können auch Kistennummern gesucht werden, um sämtliche Medien, die sich darin befinden, anzuzeigen.</p>
					</div>
			</div>	
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
			<div class="input-group">
				  <input type="text" id="searchtext" class="form-control search-query" value="" placeholder="Signatur eingeben" />
				  <span class="input-group-btn">
					<button class="btn bw" id="search" type="button">
						<i class="fa fa-search" aria-hidden="true"></i>
					</button>
				  </span>
			</div>

			<div id="result"></div>
<?php
?>
			</div>
		</div>
			
		</div>
	   </div>
		
	   <div class="row">

	   </div>
	</div>

	
<?php

?>
<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>

<script>

function loadResult() {
	  text = $(".search-query").val();
	  console.log( text );
	  if( text.length <= 2 ) { 
		$("#result").html("");
		return;
	  }
	$("#result").load('kistesig.load.php?signatur='+encodeURIComponent(text), function() {
//                alert("loaded");
            });
}
   
function init() {
   $(".search-query").keyup(function() {
	   loadResult();
   });
   
	$('#MTModal').on('shown.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var kiste = button.data('kiste') // Extract info from data-* attributes
	  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	  var modal = $(this)
	  modal.find('.modal-title').html('Regal <b>' + kiste.substring( 0, 1 ) + '</b> Kiste <b>' + kiste.substring( 1 ));
	  body = modal.find('.modal-body');
	  body.empty();
	  body.append( '<div class="renderer"></div>')

	  renderer = modal.find( '.renderer' );
	  renderer.height( '400px');
	  width = body.width();
	  renderer.width( width );
	  init3D( kiste );
	})

	$('#MTModal').on('hidden.bs.modal', function (event) {
	  var modal = $(this)
	  mediathek.stopAnimate();
	  renderer = modal.find( '.renderer' );
	  renderer.empty();
	  mediathek = null;
	  mediathek3D = null;
	})
	
   
   
}
</script>   
<?php
echo mediathekfooter();
?>

  
