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

echo mediathekheader('gatekeeper', 'Texte aus dem Dunstkreis • nun gehört!', '', array( '../css/dataTables.bootstrap4.min.css' ));
?>

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
  <div class="clearfix full-height">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                    <h2 class="small-heading">Texte aus dem Dunstkreis<br />nun gehört!</h2>
                  </div>
      </div>
      <div class="row">
      <div class="col-md-3">&nbsp;</div>
      <div class="col-md-6">

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<h4>Di 6.3.18 / Mi 7.3.18 • 19 h</h4>
<p>
  performatives Radio, Texte, vorgelesen von lesefreudigen Künstler*innen.<br />
  «Ausgewählte Texte werden für ein Publikum und die ‹digitale See› vorgetragen und aufgezeichnet.
  In unseren Kreisen werden viele Texte geschrieben, Bücher produziert doch viel zu selten und von
  zu wenigen gelesen. Wir aber wollen unsere Aufmerksamkeit gerade auf diese Textproduktion
  richten. Warum sollen diese Texte einsam bleiben? Denn vorgelesene Texte sind deshalb so
  schön, weil man beim Zuhören so gut denken kann.» Muda Mathis und Chris Regn<br />
  Amerbach Studios • Amerbachstrasse 55 a
</p>

<p>
  <center><table border=0 style="width: 100%;">
    <tr>
      <td style="width: 100%; vertical-align: top; text-align: center;">
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.F5XJ3PVL">Alice Wilke liest aus "13"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.GDQTUDW3">Ariane Koch liest aus Venus Electra Ryter: Aus einem freien Gestus inszenierter Weichheit, 2011-2017.</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.ZVSZDX4N">Birgit Kempker liest aus Renate Rasp: Ein ungeratener Sohn</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.GIHLRUM6">Chantal Küng liest "Im Dunstkreis"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.L33DECM2">Christa Ziegler liest aus Quinn Latimer: Zeichen, Laute, Metalle, Feuer: oder die Ökonomie ihrer Leser_innen.</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.IPIUXV79">Daniel Brugger liest aus Gregor Weichbrodt: Dictionary of non-notable Artists.</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.WEWUJ73M">Linda Cassens liest "Im Dunstkreis"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.VTZ25V3T">Lorenz Wiederkehr liest "Im Dunstkreis"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.HAULP53X">Lysann König liest "Im Dunstkreis"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.TJV799JY">Sabine Gebhardt Fink liest "Im Dunstkreis"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.XNP6VRDL">Susan Kobler liest "Im Dunstkreis"</a><br />
        <a href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2260611.VNWDJWAT">Katharina Bochsler und Margarit Lehmann lesen "Im Dunstkreis"</a><br />

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
