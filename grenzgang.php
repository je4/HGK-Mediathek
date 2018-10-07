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

echo mediathekheader('gatekeeper', 'Grenzgang', '', array( '../css/dataTables.bootstrap4.min.css' ));
?>

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
  <div class="clearfix full-height">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-12">
              <h2 class="small-heading">Grenzgang<br /><font size="5pt">Künstlerische Untersuchungen zur Wahrnehmung und<br />Vermittlung von Raum im trinationalen Grenzgebiet</font></h2>
                  </div>
      </div>
      <div class="row">
      <div class="col-md-3">&nbsp;</div>
      <div class="col-md-6">

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<p><center><img src="https://ba14ns21403.fhnw.ch/mediaserver/dev/app/index.php/zotero_2180340/2180340.7HGXXC5D/resize/size650x480" style="width:100%;"/></center></p>
<p>
Geographischer Ansatzpunkt des Projektes Grenzgang ist der vielseitig genutzte, aber durch die Grenzsituation nicht vollständig durchstrukturierte Raum im Dreiländereck Schweiz - Frankreich - Deutschland. Auf der Basis künstlerischer Forschungsmodi sollen Veränderungen dieses Gebietes durch Nutzungen, Grenzziehungen und Zufälligkeiten jenseits der Planung erfahrbar werden, die neue Vorstellungen von Raum etablieren können. Damit soll ein Diskussionsbeitrag geleistet werden, wie anstehende Veränderungen aus einer künstlerisch reflektierten, wahrnehmungsorientierten Sicht analysiert und erweitert werden können. Einen konzeptionellen Ausgangspunkt bilden die Schriften Lucius Burckhardts zur Spaziergangswissenschaft. Darin betont Buckhardt den Zusammenhang zwischen den Bildern in unseren Köpfen und dem, was wir im Raum wahrnehmen.
</p>
<p>
  <form class="form-inline" method=GET action="search.php">
    <input type="hidden" name="facets[catalog][]" value="Grenzgang2" />
    <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="inlineFormInput" placeholder="Suchbegriff" name="search">
    <button type="submit" class="btn btn-primary">Suchen</button>
  </form>
</p>
<p>
Künstlerische Arbeiten, die einerseits den Spaziergang als Wahrnehmung von Raum und zur Datenerhebung nutzen (sowohl historische als auch zeitgenössische), oder ihn andererseits als Aktion im öffentlichen Raum sehen, bilden wesentliche Anhaltspunkte für das Vorgehen.Das Projekt Grenzgang agiert als Teamforschungsprojekt zwischen dem Institut Lehrberufe für Gestaltung und Kunst (LGK) der Hochschule für Gestaltung und Kunst FHNW und der Abteilung Forschung und Entwicklung der Hochschule für Musik FHNW. Grundlage des Projektes bilden gemeinsame Spaziergänge des Forschungsteams im trinationalen Grenzgebiet um Basel. Die Forschenden bringen vor dem Hintergrund der eigenen Disziplin - Musik, Bildende Kunst und Kunstvermittlung - ihre spezifischen Strategien ein, um individuelle, künstlerische Raumprotokolle zu erstellen. Die hieraus resultierende Datensammlung bildet bewusst ein Konglomerat unterschiedlicher Wahrnehmungs- und Ausdrucksweisen zum Grenzraum, das dem gesamten Forschungsteam zur Verfügung gestellt wird. Regelmässige workshops dienen als milestones und disziplinübergreifender Diskursort mit internationalen Experten.Angestrebt wird eine dichte Vernetzung des Denkens im und über den Grenzraum um Basel. Ausgewertet wird das Datenmaterial in einer methodischen Triangulation (im Sinne der Verknüpfung unterschiedlicher Arbeitsweisen der Raumwahrnehmung): Die Hochschule für Musik steuert zu diesem Zweck ein Forschungsinterface bei, das unterschiedliche Quellen wie Videos, Bilder, Töne, Text und Musik in der INTERPRETATION verbinden kann. Seitens des Instituts LGK wird eine zweite Auswertung in einer INSTALLATION die Daten räumlich zueinander in Beziehung setzen, eine dritte Auswertung, ebenfalls seitens des Instituts LGK, verknüpft das Potential der gesammelten Daten zu einer PERFORMANCE. Das Projekt zielt jedoch nicht auf eine Synthetisierung dieser Arbeitsweisen und der aufgearbeiteten Daten, sondern erbringt einen Beitrag zur Diskussion der epistemischen Qualitäten künstlerischer und vermittlerischer Praxis. Diese korrelierend sollen Forschungsmodi begründet und erprobt werden, die die Wahrnehmung von Raum und dessen Generierung im Gehen aufzeigen und zur Erkundung des Grenzraumes einbringen.Begleitet wird das Forschungsprojekt Grenzgang durch das von Pro Helvetia im Rahmen des Programmes «Triptic - Kulturaustausch am Oberrhein» geförderte „Trinationale Festival des Spazierens”, das eine Art Live-Recherche ermöglicht, dem internationalen Austausch dient und die Inhalte des Forschungsprojektes in einem frühen Stadium in eine öffentliche Diskussion bringt.
</p>
<p>
  <table border=0 style="width: 100%;">
    <tr>
      <td style="width: 33%; vertical-align: top;">
        <h4>Walks</h4>
        <a href="detail.php?id=zotero-2180340.CP2KRKQX&q=3df637f0f6673fb8c86e6d356a2638bc&page=0&pagesize=25">Walk I</a><br />
        <a href="detail.php?id=zotero-2180340.QZHR4VB8&q=3df637f0f6673fb8c86e6d356a2638bc&page=0&pagesize=25">Walk II</a><br />
        <a href="detail.php?id=zotero-2180340.49AC2K6P&q=3df637f0f6673fb8c86e6d356a2638bc&page=0&pagesize=25">Walk III</a><br />
        <a href="detail.php?id=zotero-2180340.2KXS6BNF&q=3df637f0f6673fb8c86e6d356a2638bc&page=0&pagesize=25">Walk IV</a><br />
        <a href="detail.php?id=zotero-2180340.THBFWHP9&q=3df637f0f6673fb8c86e6d356a2638bc&page=0&pagesize=25">Walk V</a><br />
        <a href="detail.php?id=zotero-2180340.V9YJBYKX&q=3df637f0f6673fb8c86e6d356a2638bc&page=1&pagesize=25">Walk VI</a><br />
        <a href="detail.php?id=zotero-2180340.A6JX2K26&q=3df637f0f6673fb8c86e6d356a2638bc&page=1&pagesize=25">Walk VII</a><br />
        <a href="detail.php?id=zotero-2180340.SMSINKN6&q=3df637f0f6673fb8c86e6d356a2638bc&page=2&pagesize=25">Walk VIII</a><br />
        <a href="detail.php?id=zotero-2180340.5ATD8G7F&q=3df637f0f6673fb8c86e6d356a2638bc&page=0&pagesize=25">Walk IX</a><br />
        <a href="detail.php?id=zotero-2180340.FI2SU4TP&q=519e0569d6028b532328d5c9aa1f0809&page=0&pagesize=25">Walk X</a><br />
        <a href="detail.php?id=zotero-2180340.FSBW59VQ&q=55db49eb4034e57ef2e1e45d9c0bde5c&page=0&pagesize=25">Walk mit Gast</a><br />
      </td>
      <td style="width: 33%; vertical-align: top;">
        <h4>Events</h4>
      </td>
      <td style="width: 33%; vertical-align: top;">
        <h4>KünsterInnen</h4>
      </td>
    </tr>
  </table>
</p>
          </div>
					</div>
          <div class="col-md-3">&nbsp;</div>

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
