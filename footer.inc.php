<?php
global $session;

$footer_js = array(
  "assets/js/jquery-2.1.3.min.js",
  "assets/js/bootstrap.min.js",
  "assets/js/modernizr.js",
  "assets/js/jquery.easing.min.js",
  "assets/js/jquery.mixitup.min.js",
  "assets/js/jquery.popup.min.js",
  "assets/js/owl.carousel.min.js",
  "assets/js/contact.js",
  "assets/js/script.js",
  "js/mediathek_helper.js",
  "js/mediathek_gui.js",
  "js/md5.min.js",
  "js/tether.min.js",
  "js/bootstrap-editable.js",
  "js/jstree.min.js"

);

function addJS( $js ) {
  global $footer_js;
  if( is_array( $js )) {
      $footer_js = array_merge( $footer_js, $js );
  }
  else {
    $footer_js[] = $js;
  }
}


function mediathekfooter( $jss = array()) {
  global $footer_js;
    ob_start();
	if( DEBUG && false ) {
		echo "<pre>".print_r( $_SERVER, true )."</pre>";
		echo "<pre>".print_r( $_REQUEST, true )."</pre>";
	}
  foreach( $footer_js as $js ) {
    echo '<script type="text/javascript" src="'.$js.'"></script>'."\n";
  }
?>

<?php
foreach( $jss as $js ) {
?>
    <script type="text/javascript" src="<?php echo $js; ?>"></script>
<?php
}
 ?>
	<script language="javascript">
	   $( document ).ready(function() {
		   return;

		   init();
		   	$('#MTModal').on('shown.bs.modal', function (event) {
				var button = $(event.relatedTarget) // Button that triggered the modal
				var user = button.data('user') // Extract info from data-* attributes
				if ( typeof user == 'undefined' ) {
				  return;
				}
				// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
				// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
				var modal = $(this)
				modal.find('.modal-title').html(user);
				body = modal.find('.modal-body');
				body.empty();

				var str = '<p><?php global $session; echo htmlspecialchars( $session->shibGetMail()); ?><br /><b>Gruppen</b><br /><?php
global $session;
foreach( $session->getGroups() as $grp) {
	echo htmlspecialchars( $grp )."<br />";
}
?>';

				body.append( str );
			  })

			  $('#MTModal').on('hidden.bs.modal', function (event) {
				var modal = $(this)
				var button = $(event.relatedTarget) // Button that triggered the modal
				var user = button.data('user') // Extract info from data-* attributes
				if ( typeof user == 'undefined' ) {
				  return;
				}
			  })

	   });
	</script>

</body>
</html>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
?>
