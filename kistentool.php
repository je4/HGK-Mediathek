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
<div class="modal fade" id="3DModal" tabindex="-1" role="dialog" aria-labelledby="3DModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div>
          <iframe width="100%" height="100%" style="min-height: 350px;" src="" scrolling="no"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

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
   $(".search-query").change(function() {
	   loadResult();
   });
   
   
}
</script>   
<?php
echo mediathekfooter();
?>

  
