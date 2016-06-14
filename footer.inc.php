<?php
function mediathekfooter() {
    ob_start();
?>
  
	<script src="js/jquery.min.js"></script>
	<script src="js/tether.min.js"></script>  
	<script src="js/bootstrap.js"></script>  
	<script src="js/md5.min.js"></script>  
	<script src="js/mediathek_helper.js"></script>  
	
	<script language="javascript">
	   $( document ).ready(function() {
		   init();
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