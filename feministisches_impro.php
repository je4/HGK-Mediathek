<?php
namespace Mediathek;
/*


Fatal error: Uncaught ADODB_Exception: mysqli error: [1062: Duplicate entry '1' for key 'PRIMARY'] in EXECUTE("INSERT INTO session (`uniqueID`, `Shib-Session-ID`, certEmail, `php_session_id`, clientip ) VALUES ( '19420@fhnw.ch' , '_ddb623275354ede4f9a51104ef75ce7f' , NULL , '1' , '10.239.240.231' )") in /data/www/vhosts/mediathek.fhnw.ch/html/dev/lib/adodb5/adodb-exceptions.inc.php:80 Stack trace: #0 /data/www/vhosts/mediathek.fhnw.ch/html/dev/lib/adodb5/adodb.inc.php(1235): adodb_throw('mysqli', 'EXECUTE', 1062, 'Duplicate entry...', 'INSERT INTO ses...', false, Object(ADODB_mysqli2)) #1 /data/www/vhosts/mediathek.fhnw.ch/html/dev/lib/adodb5/drivers/adodb-mysqli2.inc.php(63): ADOConnection->_Execute('INSERT INTO ses...', false) #2 /data/www/vhosts/mediathek.fhnw.ch/html/dev/lib/adodb5/adodb.inc.php(1199): ADODB_mysqli2->_Execute('INSERT INTO ses...', false) #3 /data/www/vhosts/mediathek.fhnw.ch/html/dev/lib/Mediat in /data/www/vhosts/mediathek.fhnw.ch/html/dev/lib/adodb5/adodb-exceptions.inc.php on line 80

*/

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

echo mediathekheader('gatekeeper', 'Feministisches* Improvisatorium', '', array( '../css/dataTables.bootstrap4.min.css' ));
?>

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
  <div class="clearfix full-height">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                    <h2 class="small-heading">Feministisches* Improvisatorium</h2>
                  </div>
      </div>
      <div class="row">
      <div class="col-md-3">&nbsp;</div>
      <div class="col-md-6">

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<h4>14. bis 28. August 2018</h4>
<p>
  Was wollen wir gemeinsam auf die Beine stellen? Es ist Raum und Zeit für Vergessenes, Neues,
  Planungs- und Handlungsperspektiven. Es gibt Präsentationen, eine wachsende Fotosammlung, wofür
  euer Bilderfundus gefragt ist, Arbeitstische und Videostationen. Informationen, Projekte,
  Druckerzeugnisse aller Art, Manifeste und Forschungsergebnisse finden Platz. Die Feminismus Map
  Basel ist aufgespannt. Wir möchten an der grossen Karte über die feministischen Bewegungen, in
  ihrer Fülle und Diversität von den Anfängen bis morgen, weiterschreiben. Reichtum an Perspektiven
  schärft Blick und Feder.
</p>
<iframe style="width: 100%; height:300px; border: none" src="https://ba14ns21403-sec1.fhnw.ch/mediasrv/DigitaleSee/2018.8_Interview_feministisches_Improvisatorium_Feminismusmap_02_web.jpg/iframe/bgcolorffffff"  webkitAllowFullScreen mozAllowFullScreen allowFullScreen></iframe>
<p>
  <center><table border=0 style="width: 100%;">
    <tr>
      <td style="width: 100%; vertical-align: top; text-align: center;">
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.4GL9DPE5&q=9a343249ec50f056677b4cbb3cec5e06">Anita Fetz befragt von Katha Baur</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.RRLF6HUA&q=9a343249ec50f056677b4cbb3cec5e06">Katha Baur befragt von Anita Fetz</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.XXBIJ746&q=9a343249ec50f056677b4cbb3cec5e06">Kathrin Ginggen befragt von Muda Mathis</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.MGB6WSAD&q=9a343249ec50f056677b4cbb3cec5e06">Lena Rérat befragt von Muda Mathis</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.AY5QBK5Y&q=9a343249ec50f056677b4cbb3cec5e06">Patricia Purtschert befragt von Muda Mathis</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.4H8EE32H&q=9a343249ec50f056677b4cbb3cec5e06">Annemarie Pfister befragt von Chis Regn</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.4WDVVEWY&q=9a343249ec50f056677b4cbb3cec5e06">Elisabeth Freivogel befragt von Chris Regn</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.C7JP4BQS&q=9a343249ec50f056677b4cbb3cec5e06">Ingrid Rusterholz befragt von Chris Regn</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.WQGHXPMW&q=9a343249ec50f056677b4cbb3cec5e06">Lovis befragt von Chris Regn</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.2DYZS6XY&q=9a343249ec50f056677b4cbb3cec5e06">Marianne Mattmüller befragt von Chris Regn</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/detail.php?id=zotero-2260611.IJYPYT3I&q=9a343249ec50f056677b4cbb3cec5e06">Line Boser & Lysan König Befragen Sich.</a><br />
      </td>
    </tr>
  </table></center>
</p>
          </div>
					</div>

				</div>
			</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Copyright &copy; 2017
							<a href="#">Mediathek</a>
						</p>
					</div>

					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="author">
							<a href="http://www.fhnw.ch/hgk" target="_blank">HGK FHNW</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<?php
//include( 'bgimage.inc.php' );
?>
<script>

function init() {

}
</script>


<?php
echo mediathekfooter( array( ));
?>
