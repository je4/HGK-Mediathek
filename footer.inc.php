<?php
global $session;

function mediathekfooter() {
    ob_start();
	if( DEBUG && false ) { 
		echo "<pre>".print_r( $_SERVER, true )."</pre>";
		echo "<pre>".print_r( $_REQUEST, true )."</pre>";
	}
?>
  
    <script type="text/javascript" src="assets/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.mixitup.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.popup.min.js"></script>
    <script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="assets/js/contact.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script type="text/javascript" src="js/mediathek_helper.js"></script>
    <script type="text/javascript" src="js/mediathek_gui.js"></script>
	<script type="text/javascript" src="js/md5.min.js"></script>  
	<script type="text/javascript" src="js/tether.min.js"></script>  
	<script type="text/javascript" src="js/bootstrap-editable.js"></script>  
	<script type="text/javascript" src="js/jstree.min.js"></script>  
	
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