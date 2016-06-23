<?php
namespace Mediathek;

require '../navbar.inc.php';
require '../searchbar.inc.php';
require '../header.inc.php';
require '../footer.inc.php';

include( '../init.inc.php' );

global $db;

header( 'Location: '.$_REQUEST['target'] );

echo mediathekheader('login', null);
?>
	<div class="container-fluid" style="margin-top: 0px; background-color: rgba(255, 255, 255, 0.5); padding: 20px;">
		<pre>
			<?php print_r($_SERVER); ?>
		</pre>
	</div>

	
<?php

?>
<script>


function init() {
	window.location = <?php echo $_REQUEST['target']; ?>;
}
</script>   
<?php
echo mediathekfooter();
?>

  
