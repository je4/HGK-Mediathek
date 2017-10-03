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

echo mediathekheader('gatekeeper', 'Performance Chronik Basel', '', array( '../css/dataTables.bootstrap4.min.css' ));
?>

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
  <div class="clearfix full-height">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                    <h2 class="small-heading">Online Zugang zur Performance Chronik Basel</h2>
                  </div>
      </div>
      <div class="row">
      <div class="col-md-3">&nbsp;</div>
      <div class="col-md-6">

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<h4>Videosammlung der Performance Chronik Basel (1987- 2006)</h4>

<p>
  Die Sammlung der Performance Chronik Basel besteht aus Videodokumentationen
  von Live Performances die während der Recherche in der Zeit zwischen 2012/16, 
  für die zweite Publikation „Aufzeichnen und Erinnern“ (Diaphanes Verlag 2016 )
  die, die zwei Dekaden der Performancegeschichte von 1987 - 2006 beleuchten.
  Das Material wurde direkt von den Künstler_innen persönlich eingesammelt und
  digitalisiert. Die Sammlung ist lückenhaft und soll laufend ergänzt werden.
</p>

<p>Nun fliesst diese digitale Sammlung in die <a href="http://www.kasko.ch/2012/index.php?/aktuell/1429117--am-ufer-der-digitalen-see/" target="_blank">Digitale See</a>.</p>
<p>
  <form class="form-inline" method=GET action="search.php">
    <input type="hidden" name="facets[catalog][]" value="PCB_Basel" />
    <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="inlineFormInput" placeholder="Suchbegriff" name="search">
    <button type="submit" class="btn btn-primary">Suchen</button>
  </form>
</p>

<p>Die Digitale See ist kein Ort, sondern eine Idee und Haltung. Und hat zum
  Ziel, Kunst, in diesem Fall Performance Dokumentationen, der freien ungebunden
  Szene im Internet zugänglich zumachen. 
  Es geht nicht in erster Linie um Schutz, Aufbewahrung oder Kontrolle über das
  Material, sondern darum: dieses dokumentarische, kunsthistorische Material den
  Personen, die damit arbeiten wollen, in der Lehre, in der Forschung, in der
  eigenen künstlerischen Recherche zugänglich zu machen, um eine Rezeption zu
  ermöglichen und es in diesen Feldern (Lehre, Forschung, künstlerische Recherche)
  uneingeschränkt veröffentlichen und sichtbar machen zu können.
</p>
<a href="http://www.performacechronikbasel.ch">www.performacechronikbasel.ch</a>

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
