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
<p><center><img src="https://ba14ns21403-sec1.fhnw.ch/mediasrv/zotero_2250437/Grenzgang.jpg/resize/size650x480" style="width:100%;"/></center></p>
<p>
Geographisch im trinationalen Grenzgebiet um Basel angesiedelt, verortet sich Grenzgang methodisch zwischen Künstlerischer Forschung und der Promenadologie Lucius Burckhardts. Das Projekt hat zwei zentrale Zielsetzungen: Es geht um die Frage, wie wir Raum, spezifisch den trinationalen Raum der Region Basel, wahrnehmen. Und darum, wie der Modus künstlerischer Forschung in einer als diskursive Praxis verstandenen Kunstvermittlung wirksam werden kann.
</p>
Um aufzeigen und diskutieren zu können, wie sich Wissen in einem Projekt generiert, das explizit im Modus künstlerischer Forschung agiert, war eine detaillierte Dokumentation der Vorgehensweisen und entstehenden Notationen und Umsetzungen unabdingbar. Was ebenso für den Übertrag in die Kunstvermittlung gilt: relevant sind für eine epistemische Praxis der Kunstvermittlung die Arbeitsweisen, Zwischenschritte, aus Notationen und Umsetzungen folgenden offenen Fragen, die Brüche und Irritationen, die sich aus dem künstlerischen Forschungsmodus ergeben. Die Datenbank Grenzgang will genau diese Arbeitsweisen, Zwischenschritte, Brüche, Irritationen, aber auch hieraus abzuleitende Konsequenzen und Ergebnisse aufzeigen und in die Diskussion einspeisen.
<p>
</p>
<p>
  <form class="form-inline" method=GET action="search.php">
    <input type="hidden" name="facets[catalog][]" value="Grenzgang" />
    <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="inlineFormInput" placeholder="Suchbegriff" name="search">
    <button type="submit" class="btn btn-primary">Suchen</button>
  </form>
</p>
<p>
  <ul>

    <li><a target="_blank" href="https://ba14ns21403-sec1.fhnw.ch/mediasrv/zotero_2250437/2250437.2XH6UNCD_enclosure/iframe">Schlussbericht Schweizer Nationalfond</a></li>
    <li><a target="_blank" href="https://link.springer.com/chapter/10.1007/978-3-319-76992-9_11">Springer Text Grenzgang. When Promenadology Meets Library</a></li>
    <li><a target="_blank" href="https://ba14ns21403-sec1.fhnw.ch/mediasrv/zotero_2250437/2250437.3WG4QMQU_enclosure/iframe">Ortszeit DE: Grenzgang. Vom Dreispitz in den trinationalen Raum</a></li>
    <li><a target="_blank" href="https://ba14ns21403-sec1.fhnw.ch/mediasrv/zotero_2250437/2250437.5LZJ3Q26_enclosure/iframe">Ortszeit EN: Grenzgang. From Dreispitz to the space of the trinational region</a></li>
    <li><a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.FHAWAZ6M">Grenzgang – Laying a Keyword Path. 15th ELIA Biennial Conference Rotterdam: Resilience and the City</a></li>
  </ul>
</p>
<p>
  <table border=0 style="width: 100%;">
    <tr>
      <td style="vertical-align: top;">
        <h4>Output</h4>
        Salon Mondial<br />
        Tischgespräche<br />
        Arbeit mit Schulklassen<br />
        Konzert HEK<br />
        IBA Talk<br />
        IBA Walk 1<br />
        IBA Walk 2<br />
        Spaziergang semipermeabel<br />
        Walk Giswil<br />
        Walk Rotterdam
      </td>
      <td style="vertical-align: top;">
        <h4>Fundus</h4>
        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.BZMJ8X2H">Balades-de-Bâle (Markus Schwander)</a></br>
        <!-- a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.5FQCFRLB" -->Miniatures (Amadis Brugnoni)<!-- /a --></br>
        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.BQQIGAZ6">Nullmeterzeichnungen (Markus Schwander)</a></br>
        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.6ZMQS5QV">Raumklangskizzen (Daniel Brefin)</a></br>
        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.73GBIEA5">Videos entlang (Simone Etter)</a>
      </td>
      <td style="vertical-align: top;">
        <h4>Walks</h4>
        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.QPSVJZYD">Walk 1</a></br>

        Walk 2</br>
        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.VNCJN3PN">Walk 3</a></br>

        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.PSSMPXG3">Walk 4</a></br>

        <a target="_blank" href="https://mediathek.hgk.fhnw.ch/amp/detail/zotero2-2250437.3A9T6FYM">Walk 5</a></br>

        Walk 6<br />
        Walk 7<br />
        Walk 8<br />
        Walk 9<br />
        Walk 10

      </td>
    </tr>
  </table>
</p>
<p><center><iframe src="https://ba14ns21403-sec1.fhnw.ch/mediasrv/zotero_2250437/Grenzgang_Doku_Salon_Mondial.mp4/iframe" frameBorder=0 style="width:100%;height:400px" class="" webkitAllowFullScreen mozAllowFullScreen allowFullScreen></iframe></center></p>
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
