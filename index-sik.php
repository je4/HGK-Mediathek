<?php
require 'init.inc.php';
require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

echo mediathekheader('', null);
?>
	<div class="container-fluid" style="margin-top: 20px; padding: 20px;">
		<div class="row">
		  <div class="col-lg-offset-2 col-lg-8">
<?php
	echo searchbar(null, null);
?>
		</div>
	   </div>
	<script>
function init() {
	initSearch("all");  
}
	</script> 	
<?php
echo mediathekfooter();
?>

  
