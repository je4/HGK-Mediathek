<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

global $db;

echo mediathekheader('home', null);
?>
	<div class="container-fluid" style="margin-top: 0px; background-color: rgba(255, 255, 255, 0.5); padding: 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-12">
			<div class="jumbotron jumbotron-fluid" style="background: url('img/mt_small.jpg') no-repeat center center;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			background-size: cover;
			-o-background-size: cover;">
				<div class="container" style="background-color: rgba(255, 255, 255, 0.8);">
					<h1>Herzlich Willkommen in der Mediathek</h1>
					<p>Die Mediathek der HGK stellt wissenschaftliche Literatur und elektronische Medien aus den 
					Bereichen Bildende Kunst, Design, Designtheorie, Innenarchitektur und Szenografie zur Verfügung. 
					Sie richtet sich an Studierende, Dozierende und Forschende der HGK, der FHNW sowie externe Interessierte. 
					Die Mediathek ist dem NEBIS - Netzwerk von Bibliotheken und Informationsstellen in der Schweiz
					angeschlossen. Ihre Benutzung ist kostenlos.</p>
				</div>
			</div>	
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
<?php
	echo searchbar(null, null);
?>
			</div>
		</div>
			
		</div>
	   </div>
		
	   <div class="row">
		  <div class="col-md-9">
		  
			<table class="table" style="table-layout: fixed;">
				<tr>
					<td class="list" style="text-align: right; width: 30%;">
						<h4>Einschreibung &nbsp;<i class="fa fa-pencil-square-o" aria-hidden="true"></i></h4>
					</td>
					<td class="list" style="width: 70%;">
						<p>Die Mediathek schaltet <b>Angehörigen der FHNW</b> ihre FH-Cards für die Bibliotheksnutzung frei. 
						Wer bereits in einer IDS-Bibliothek eingeschrieben ist, brauch keine separate Anmeldung. 
						Die Benutzerausweise und Passworte sind auch im NEBIS gültig.</p>
						<p><b>Erstnutzende</b> füllen zuvor bitte das Einschreibeformular aus.<br />
						Wir empfehlen die Mediathek (FHNW-GE) als Stammbibliothek.</p>
						<p><b>Externe Benutzerinnen und Benutzer</b>, die in keine NEBIS- oder IDS-Bibliothek eingeschrieben 
						sind, registrieren sich ebenfalls online über das NEBIS-Einschreibeformular und erhalten in der 
						Mediathek dann einen NEBIS-Bibliotheksausweis. Bei der ersten Ausleihe werden die amtlichen Angaben überprüft. 
						Die Anmeldung und Benutzung ist kostenlos.</p>
					
					</td>
				</tr>
				<tr>
					<td class="list" style="text-align: right; width: 30%;">
						<h4>Benutzerkonto &nbsp;<i class="fa fa-indent" aria-hidden="true"></i> </h4>
					</td>
					<td class="list" style="width: 70%;">
						<p>Das Benutzungskonto bietet jederzeit online einen Überblick über aktuelle Bestellungen, Reservationen, 
						Gebühren sowie die jeweiligen Ausleihen inkl. Leihfristen. Auch die Adressdaten (E-Mail-, Telefonnummer, 
						Wohn-/Korrespondenzadresse können bequem online verändert werden.</p>
					</td>
				</tr>
				<tr>
					<td class="list" style="text-align: right; width: 30%;">
						<h4>Gebühren &nbsp;<i class="fa fa-btc" aria-hidden="true"></i></h4>
					</td>
					<td class="list" style="width: 70%;">
						<p><b>Basisleistungen</b></p>
						<p><table>
							<tr><td style="padding: 1px 5px 1px 1px; width: 70%;">Ausleihe im NEBIS-Verbund via Online-Katalog</td><td style="padding: 1px 1px 1px 5px; width: 30%;">kostenlos</td></tr>
							<tr><td style="padding: 1px 5px 1px 1px; width: 70%;">Benutzungsausweis</td><td style="padding: 1px 1px 1px 5px; width: 30%;">kostenlos</td></tr>
						</table></p>
						<p><b>Mahnung nach Ablauf der Leihfrist pro Dokument</b></p>
						<p><table>
							<tr><td style="padding: 1px 5px 1px 1px; width: 70%;">Erinnerung / Rückruf</td><td style="padding: 1px 1px 1px 5px; width: 30%;">kostenlos</td></tr>
							<tr><td style="padding: 1px 5px 1px 1px; width: 70%;">1. Mahnung (nach 10 Arbeitstagen)</td><td style="padding: 1px 1px 1px 5px; width: 30%;">CHF 10,-</td></tr>
							<tr><td style="padding: 1px 5px 1px 1px; width: 70%;">2. Mahnung (nach weiteren 10 Arbeitstagen)</td><td style="padding: 1px 1px 1px 5px; width: 30%;">CHF 20,-</td></tr>
							<tr><td style="padding: 1px 5px 1px 1px; width: 70%;">3. Mahnung (nach weiteren 10 Arbeitstagen, mit Kontosperrung)</td><td style="padding: 1px 1px 1px 5px; width: 30%;">CHF 35,-</td></tr>
						</table></p>
						<p>Bitte überprüfen Sie regelmässig den Stand der Ausleihen in Ihrem Benutzungskonto. Der Versand der Erinnerungen ist 
						eine Zusatzdienstleistung, für deren Erhalt (Spam-Filter etc.) wir keine Gewähr übernehmen können. Siehe <a href="http://www.nebis.ch/FAQ">NEBIS FAQ</a></p>
						<p><b>Ersatzbeschaffung</b></p>
						<p>Beim Verlust eines Mediums wird eine Ersatzbeschaffung veranlasst. Die Ersatzbeschaffung kann entweder von der 
						Mediathek oder vom Nutzer durchgeführt werden. Zusätzlich entfällt eine Bearbeitungsgebühr von CHF 20.-/Medium, 
						welche der Benutzerin oder dem Benutzer belastet wird.</p>
						<p>Beschafft die Mediathek die Ersatzdokumente, kommen zur Bearbeitungsgebühr (CHF 20.-) die Kosten des Dokuments hinzu.</p>
						<p>(Alle Beträge inkl. Mehrwertsteuer)</p>
					</td>
				</tr>
				<tr>
					<td class="list" style="text-align: right; width: 30%;">
						<h4>Sonderleistungen &nbsp;<i class="fa fa-plus-square" aria-hidden="true"></i>
</h4>
					</td>
					<td class="list" style="width: 70%;">
						<p>Anschaffungsvorschläge werden via E-Mail () vorgebracht.</p>
						<p>Medien der Zentralbibliothek Zürich (ZB) können in die Mediathek zur Ansicht bestellt werden.</p>
						<p>Bitte beachten: es ist nur eine Konsultation vor Ort (inkl. Scannen / Kopieren) aber keine Ausleihe möglich.</p>
						<p>ZB-Medien, und nur diese, werden via E-Mail () bestellt. Bitte geben Sie Autor, Titel, Jahr und Signatur im Bestellmail an.</p>
					</td>
				</tr>
			</table>
		  </div>
   		  <div class="col-md-3" style="background-color: transparent;">
			<div style="; font-weight: bold;">Öffnungszeiten & Information</div>
			<div class="bw" style="padding:5px;">
				<p><b>Öffnungszeiten während des Semesters</b><br />
				Montag bis Freitag, 10.00 bis 18.00 Uhr</p>

				<p><b>Sommeröffnungszeiten 18.06.2016 - 15.08.2016</b><br />
				Montag bis Freitag, 10.00 bis 14.00 Uhr</p>

				<p>Wenn der Haupteingang des Hochhauses D während der Öffnungszeiten der Mediathek geschlossen ist, können Sie das Thekenpersonal 
				anrufen (+41 (0)61 228 41 84). Sie werden dann abgeholt und eingelassen.</p>
			</div>
<!--			<div style="padding-top: 5px; font-weight: bold;">Sonderschliessungen</div>
				<div class="bw" style="padding:5px;">
				<p><b>Aktuell nicht genutzt</b><br />
				Wir danken Ihnen für Ihr Verständnis</p>
-->
			<div style="padding-top: 8px; font-weight: bold;">Auskünfte</div>
				<div class="bw" style="padding:5px;">
				<p>Info-Theke: +41 (0)61 228 41 84<br />
				Mail:</p>
			</div>

			<div style="padding-top: 8px; font-weight: bold;">Ansprechpersonen</div>
				<div class="bw" style="padding:5px;">
				<p>Dr. Tabea Lurk<br />
				Leitung Mediathek<br />
				Mail: <br />
				T +41 61 228 43 65</p>
				<p>Jessica Baumann<br />
				I & D Fachfrau; Medienbearbeitung<br />
				Mail: <br />
				+41 (0)61 228 40 47</p>

			</div>

		</div>

	   </div>
	</div>

	
<?php

?>
<script>


function init() {
	initSearch("all");  

}
</script>   
<?php
echo mediathekfooter();
?>

  
