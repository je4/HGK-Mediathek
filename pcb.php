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

echo mediathekheader('gatekeeper', 'Performance Chronik Basel', '', array( '../css/dataTables.bootstrap4.min.css' ), ['robots'=>'nofollow']);
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
<a href="http://www.performancechronikbasel.ch">www.performancechronikbasel.ch</a>
<p>
  <section style="column-count: 3;">
    <!-- Andereggen, Ariane --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Andereggen%2C+Ariane%22">Andereggen, Ariane</a><br />

    <!-- Baumann, Iris --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Baumann%2C+Iris%22">Baumann, Iris</a><br />

    <!-- Berard, Brigitte --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Berard%2C+Brigitte%22">Berard, Brigitte</a><br />

    <!-- Bergmann, Silvia --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Bergmann%2C+Silvia%22">Bergmann, Silvia</a><br />

    <!-- Billari, Domenico --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Billari%2C+Domenico%22">Billari, Domenico</a><br />

    <!-- Blum, Martin --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Blum%2C+Martin%22">Blum, Martin</a><br />

    <!-- Bonvicini, Silvie --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Bonvicini%2C+Silvie%22">Bonvicini, Silvie</a><br />

    <!-- Bove, Gian-Cosimo --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Bove%2C+Gian-Cosimo%22">Bove, Gian-Cosimo</a><br />

    <!-- Butch&Baumann --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Butch%26Baumann%22">Butch&amp;Baumann</a><br />

    <!-- Cherait, Monrad --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Cherait%2C+Monrad%22">Cherait, Monrad</a><br />

    <!-- Chiquet, Fabian --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Chiquet%2C+Fabian%22">Chiquet, Fabian</a><br />

    <!-- Chramosta, Martin --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Chramosta%2C+Martin%22">Chramosta, Martin</a><br />

    <!-- Cuny, Philip --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Cuny%2C+Philip%22">Cuny, Philip</a><br />

    <!-- Daphi, Dorothee --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Daphi%2C+Dorothee%22">Daphi, Dorothee</a><br />

    <!-- Dellers, Thasslo --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Dellers%2C+Thasslo%22">Dellers, Thasslo</a><br />

    <!-- Derendinger, Sarah-Maria --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Derendinger%2C+Sarah-Maria%22">Derendinger, Sarah-Maria</a><br />

    <!-- Diener, Mo --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Diener%2C+Mo%22">Diener, Mo</a><br />

    <!-- Dähler, Mona Stefan --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22D%5Cu00e4hler%2C+Mona+Stefan%22">Dähler, Mona Stefan</a><br />

    <!-- Eifler, Thomas --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Eifler%2C+Thomas%22">Eifler, Thomas</a><br />

    <!-- Eriksson, Lena --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Eriksson%2C+Lena%22">Eriksson, Lena</a><br />

    <!-- Eva, Widmann --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Eva%2C+Widmann%22">Eva, Widmann</a><br />

    <!-- Focketyn, Hans --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Focketyn%2C+Hans%22">Focketyn, Hans</a><br />

    <!-- Forrer, Marcel --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Forrer%2C+Marcel%22">Forrer, Marcel</a><br />

    <!-- Frank, Regina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Frank%2C+Regina%22">Frank, Regina</a><br />

    <!-- Fuchs, Simone --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Fuchs%2C+Simone%22">Fuchs, Simone</a><br />

    <!-- GABI --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22GABI%22">GABI</a><br />

    <!-- Ganz, Haimo --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Ganz%2C+Haimo%22">Ganz, Haimo</a><br />

    <!-- Ganz, Iris --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Ganz%2C+Iris%22">Ganz, Iris</a><br />

    <!-- GanzBlum --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22GanzBlum%22">GanzBlum</a><br />

    <!-- Gmür, Martina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Gm%5Cu00fcr%2C+Martina%22">Gmür, Martina</a><br />

    <!-- Goessi, Markus --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Goessi%2C+Markus%22">Goessi, Markus</a><br />

    <!-- Gotovac, Tomislav --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Gotovac%2C+Tomislav%22">Gotovac, Tomislav</a><br />

    <!-- Grau, Pascale --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Grau%2C+Pascale%22">Grau, Pascale</a><br />

    <!-- Grossenbache, Bettina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Grossenbache%2C+Bettina%22">Grossenbache, Bettina</a><br />

    <!-- Gruppe a.b. --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Gruppe+a.b.%22">Gruppe a.b.</a><br />

    <!-- Gysin, Lukas --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Gysin%2C+Lukas%22">Gysin, Lukas</a><br />

    <!-- Gössi, Markus --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22G%5Cu00f6ssi%2C+Markus%22">Gössi, Markus</a><br />

    <!-- Günther, Monika --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22G%5Cu00fcnther%2C+Monika%22">Günther, Monika</a><br />

    <!-- Hart, Hanno --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Hart%2C+Hanno%22">Hart, Hanno</a><br />

    <!-- Hauert, Sibylle --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Hauert%2C+Sibylle%22">Hauert, Sibylle</a><br />

    <!-- Hebendanz, Johannes --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Hebendanz%2C+Johannes%22">Hebendanz, Johannes</a><br />

    <!-- Hensler, Markus --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Hensler%2C+Markus%22">Hensler, Markus</a><br />

    <!-- Hiepler, Esther --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Hiepler%2C+Esther%22">Hiepler, Esther</a><br />

    <!-- Hofer, Marianne --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Hofer%2C+Marianne%22">Hofer, Marianne</a><br />

    <!-- Huber, Judith --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Huber%2C+Judith%22">Huber, Judith</a><br />

    <!-- Häni, Daniel --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22H%5Cu00e4ni%2C+Daniel%22">Häni, Daniel</a><br />

    <!-- Ilić, Aleksandar Battista --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Ili%5Cu0107%2C+Aleksandar+Battista%22">Ilić, Aleksandar Battista</a><br />

    <!-- Iselin, Joa --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Iselin%2C+Joa%22">Iselin, Joa</a><br />

    <!-- JOKO --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22JOKO%22">JOKO</a><br />

    <!-- Jasper, Berndt --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Jasper%2C+Berndt%22">Jasper, Berndt</a><br />

    <!-- Jensen, Knut --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Jensen%2C+Knut%22">Jensen, Knut</a><br />

    <!-- Johnson, Carla --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Johnson%2C+Carla%22">Johnson, Carla</a><br />

    <!-- Josipovic, Mileva --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Josipovic%2C+Mileva%22">Josipovic, Mileva</a><br />

    <!-- Jost, Karin --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Jost%2C+Karin%22">Jost, Karin</a><br />

    <!-- Jörg, Andrina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22J%5Cu00f6rg%2C+Andrina%22">Jörg, Andrina</a><br />

    <!-- Keser, Ivana --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Keser%2C+Ivana%22">Keser, Ivana</a><br />

    <!-- Klassen, Norbert --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Klassen%2C+Norbert%22">Klassen, Norbert</a><br />

    <!-- Knut & Silvie --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Knut+%26+Silvie%22">Knut &amp; Silvie</a><br />

    <!-- Kolb, Lucie --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Kolb%2C+Lucie%22">Kolb, Lucie</a><br />

    <!-- Kopp, Regula --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Kopp%2C+Regula%22">Kopp, Regula</a><br />

    <!-- Kramer, Samy --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Kramer%2C+Samy%22">Kramer, Samy</a><br />

    <!-- Kramer, Sämi --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Kramer%2C+S%5Cu00e4mi%22">Kramer, Sämi</a><br />

    <!-- Kraushaar, Seraina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Kraushaar%2C+Seraina%22">Kraushaar, Seraina</a><br />

    <!-- Kurz, Simone --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Kurz%2C+Simone%22">Kurz, Simone</a><br />

    <!-- Les Reines Prochaines --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Les+Reines+Prochaines%22">Les Reines Prochaines</a><br />

    <!-- Lischka, Gerhard Johann --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Lischka%2C+Gerhard+Johann%22">Lischka, Gerhard Johann</a><br />

    <!-- Loher, Katja --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Loher%2C+Katja%22">Loher, Katja</a><br />

    <!-- Luping, Yue --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Luping%2C+Yue%22">Luping, Yue</a><br />

    <!-- Lüber, Heinrich --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22L%5Cu00fcber%2C+Heinrich%22">Lüber, Heinrich</a><br />

    <!-- Maag, Irene --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Maag%2C+Irene%22">Maag, Irene</a><br />

    <!-- Madörin, Fränzi --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Mad%5Cu00f6rin%2C+Fr%5Cu00e4nzi%22">Madörin, Fränzi</a><br />

    <!-- Maly, Valerina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Maly%2C+Valerina%22">Maly, Valerina</a><br />

    <!-- Marti, Hansjörg --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Marti%2C+Hansj%5Cu00f6rg%22">Marti, Hansjörg</a><br />

    <!-- Mathis, Muda --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Mathis%2C+Muda%22">Mathis, Muda</a><br />

    <!-- Moebius, Matthias --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Moebius%2C+Matthias%22">Moebius, Matthias</a><br />

    <!-- Morgen, Tony --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Morgen%2C+Tony%22">Morgen, Tony</a><br />

    <!-- Müller, Christian --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22M%5Cu00fcller%2C+Christian%22">Müller, Christian</a><br />

    <!-- Naegelin, Barbara --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Naegelin%2C+Barbara%22">Naegelin, Barbara</a><br />

    <!-- Neecke, Niki --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Neecke%2C+Niki%22">Neecke, Niki</a><br />

    <!-- Nieslony, Borys --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Nieslony%2C+Borys%22">Nieslony, Borys</a><br />

    <!-- Oechslin, Reto --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Oechslin%2C+Reto%22">Oechslin, Reto</a><br />

    <!-- Oertli, Christoph --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Oertli%2C+Christoph%22">Oertli, Christoph</a><br />

    <!-- Personnier, Gerald --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Personnier%2C+Gerald%22">Personnier, Gerald</a><br />

    <!-- Protoplast --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Protoplast%22">Protoplast</a><br />

    <!-- Publikum --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Publikum%22">Publikum</a><br />

    <!-- Ranzenhofer, Christoph --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Ranzenhofer%2C+Christoph%22">Ranzenhofer, Christoph</a><br />

    <!-- Reichmuth, Daniel --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Reichmuth%2C+Daniel%22">Reichmuth, Daniel</a><br />

    <!-- Rerat, Gabriele --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Rerat%2C+Gabriele%22">Rerat, Gabriele</a><br />

    <!-- Rickert, Kai --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Rickert%2C+Kai%22">Rickert, Kai</a><br />

    <!-- Riedweg, Walter --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Riedweg%2C+Walter%22">Riedweg, Walter</a><br />

    <!-- Ritzmann, Marion --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Ritzmann%2C+Marion%22">Ritzmann, Marion</a><br />

    <!-- Rodriguez, Sylvie --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Rodriguez%2C+Sylvie%22">Rodriguez, Sylvie</a><br />

    <!-- Rust, Dorothea --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Rust%2C+Dorothea%22">Rust, Dorothea</a><br />

    <!-- Saemann, Andrea --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Saemann%2C+Andrea%22">Saemann, Andrea</a><br />

    <!-- Saner, Clara --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Saner%2C+Clara%22">Saner, Clara</a><br />

    <!-- Saraceno, Giovanni --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Saraceno%2C+Giovanni%22">Saraceno, Giovanni</a><br />

    <!-- Schechner, Richard --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schechner%2C+Richard%22">Schechner, Richard</a><br />

    <!-- Schill, Ruedi --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schill%2C+Ruedi%22">Schill, Ruedi</a><br />

    <!-- Schiller, Christoph --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schiller%2C+Christoph%22">Schiller, Christoph</a><br />

    <!-- Schilling, Klara --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schilling%2C+Klara%22">Schilling, Klara</a><br />

    <!-- Schmid, Regina Florida --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schmid%2C+Regina+Florida%22">Schmid, Regina Florida</a><br />

    <!-- Schmidhalter, Hagar --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schmidhalter%2C+Hagar%22">Schmidhalter, Hagar</a><br />

    <!-- Schroeder, Celine --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schroeder%2C+Celine%22">Schroeder, Celine</a><br />

    <!-- Schuppe, Marianne --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schuppe%2C+Marianne%22">Schuppe, Marianne</a><br />

    <!-- Schwabe, Verena --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schwabe%2C+Verena%22">Schwabe, Verena</a><br />

    <!-- Schwarzer, Jermias --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schwarzer%2C+Jermias%22">Schwarzer, Jermias</a><br />

    <!-- Schweizer & Schweizer --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Schweizer+%26+Schweizer%22">Schweizer &amp; Schweizer</a><br />

    <!-- Schüppach, Dina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Sch%5Cu00fcppach%2C+Dina%22">Schüppach, Dina</a><br />

    <!-- Schürch, Dorothea --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Sch%5Cu00fcrch%2C+Dorothea%22">Schürch, Dorothea</a><br />

    <!-- Sibylle, Hauert --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Sibylle%2C+Hauert%22">Sibylle, Hauert</a><br />

    <!-- Sidler, Celia --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Sidler%2C+Celia%22">Sidler, Celia</a><br />

    <!-- Sidler, Nathalie --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Sidler%2C+Nathalie%22">Sidler, Nathalie</a><br />

    <!-- Siegfried, Walter --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Siegfried%2C+Walter%22">Siegfried, Walter</a><br />

    <!-- Signer, Roman --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Signer%2C+Roman%22">Signer, Roman</a><br />

    <!-- Silber, Alex --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Silber%2C+Alex%22">Silber, Alex</a><br />

    <!-- Spiess, Judith --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Spiess%2C+Judith%22">Spiess, Judith</a><br />

    <!-- Studierende HGK Bildhauerklasse --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Studierende+HGK+Bildhauerklasse%22">Studierende HGK Bildhauerklasse</a><br />

    <!-- Stäuble, Andreas --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22St%5Cu00e4uble%2C+Andreas%22">Stäuble, Andreas</a><br />

    <!-- Tan, Chen --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Tan%2C+Chen%22">Tan, Chen</a><br />

    <!-- Videoorchester --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Videoorchester%22">Videoorchester</a><br />

    <!-- Weber, Selma --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Weber%2C+Selma%22">Weber, Selma</a><br />

    <!-- Weekend Art --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Weekend+Art%22">Weekend Art</a><br />

    <!-- Widauer, Nives --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Widauer%2C+Nives%22">Widauer, Nives</a><br />

    <!-- Wüsten, Franziska --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22W%5Cu00fcsten%2C+Franziska%22">Wüsten, Franziska</a><br />

    <!-- Wüsten, Günther --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22W%5Cu00fcsten%2C+G%5Cu00fcnther%22">Wüsten, Günther</a><br />

    <!-- Z'Rotz, Tina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Z%27Rotz%2C+Tina%22">Z'Rotz, Tina</a><br />

    <!-- Zwick, Sus --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22Zwick%2C+Sus%22">Zwick, Sus</a><br />

    <!-- della Guestina, Christina --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22della+Guestina%2C+Christina%22">della Guestina, Christina</a><br />

    <!-- v. Krosigh, Marita --><a href="https://mediathek.hgk.fhnw.ch/amp/search/zotero2-2545256.4ZHXVDDJ?searchtext=author%3A%22v.+Krosigh%2C+Marita%22">v. Krosigh, Marita</a><br />


  </section>
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
