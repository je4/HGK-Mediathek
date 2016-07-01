<?php
global $session;

function mediathekfooter() {
    ob_start();
	if( DEBUG ) {
		echo "<pre>".print_r( $_SERVER, true )."</pre>";
	}
?>
  
	<script src="js/jquery.min.js"></script>
	<script src="js/tether.min.js"></script>  
	<script src="js/bootstrap.js"></script>  
	<script src="js/md5.min.js"></script>  
	<script src="js/mediathek_helper.js"></script>  
	
	<script language="javascript">
	   $( document ).ready(function() {
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

<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["setDomains", ["*.mediathek.hgk.fhnw.ch","*.mediathek.objectspace.org"]]);
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//ba14ns21400.fhnw.ch/piwik/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//ba14ns21400.fhnw.ch/piwik/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->	
</body>
</html>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
?>