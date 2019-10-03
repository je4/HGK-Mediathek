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
    <!-- Andereggen, Ariane --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Andereggen%2C+Ariane%5C%22%22%7D">Andereggen, Ariane</a><br />

    <!-- Baumann, Iris --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Baumann%2C+Iris%5C%22%22%7D">Baumann, Iris</a><br />

    <!-- Berard, Brigitte --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Berard%2C+Brigitte%5C%22%22%7D">Berard, Brigitte</a><br />

    <!-- Bergmann, Silvia --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Bergmann%2C+Silvia%5C%22%22%7D">Bergmann, Silvia</a><br />

    <!-- Billari, Domenico --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Billari%2C+Domenico%5C%22%22%7D">Billari, Domenico</a><br />

    <!-- Blum, Martin --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Blum%2C+Martin%5C%22%22%7D">Blum, Martin</a><br />

    <!-- Bonvicini, Silvie --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Bonvicini%2C+Silvie%5C%22%22%7D">Bonvicini, Silvie</a><br />

    <!-- Bove, Gian-Cosimo --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Bove%2C+Gian-Cosimo%5C%22%22%7D">Bove, Gian-Cosimo</a><br />

    <!-- Butch&Baumann --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Butch%26Baumann%5C%22%22%7D">Butch&amp;Baumann</a><br />

    <!-- Cherait, Monrad --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Cherait%2C+Monrad%5C%22%22%7D">Cherait, Monrad</a><br />

    <!-- Chiquet, Fabian --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Chiquet%2C+Fabian%5C%22%22%7D">Chiquet, Fabian</a><br />

    <!-- Chramosta, Martin --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Chramosta%2C+Martin%5C%22%22%7D">Chramosta, Martin</a><br />

    <!-- Cuny, Philip --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Cuny%2C+Philip%5C%22%22%7D">Cuny, Philip</a><br />

    <!-- Daphi, Dorothee --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Daphi%2C+Dorothee%5C%22%22%7D">Daphi, Dorothee</a><br />

    <!-- Dellers, Thasslo --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Dellers%2C+Thasslo%5C%22%22%7D">Dellers, Thasslo</a><br />

    <!-- Derendinger, Sarah-Maria --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Derendinger%2C+Sarah-Maria%5C%22%22%7D">Derendinger, Sarah-Maria</a><br />

    <!-- Diener, Mo --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Diener%2C+Mo%5C%22%22%7D">Diener, Mo</a><br />

    <!-- Dähler, Mona Stefan --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22D%5Cu00e4hler%2C+Mona+Stefan%5C%22%22%7D">Dähler, Mona Stefan</a><br />

    <!-- Eifler, Thomas --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Eifler%2C+Thomas%5C%22%22%7D">Eifler, Thomas</a><br />

    <!-- Eriksson, Lena --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Eriksson%2C+Lena%5C%22%22%7D">Eriksson, Lena</a><br />

    <!-- Eva, Widmann --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Eva%2C+Widmann%5C%22%22%7D">Eva, Widmann</a><br />

    <!-- Focketyn, Hans --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Focketyn%2C+Hans%5C%22%22%7D">Focketyn, Hans</a><br />

    <!-- Forrer, Marcel --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Forrer%2C+Marcel%5C%22%22%7D">Forrer, Marcel</a><br />

    <!-- Frank, Regina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Frank%2C+Regina%5C%22%22%7D">Frank, Regina</a><br />

    <!-- Fuchs, Simone --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Fuchs%2C+Simone%5C%22%22%7D">Fuchs, Simone</a><br />

    <!-- GABI --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22GABI%5C%22%22%7D">GABI</a><br />

    <!-- Ganz, Haimo --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Ganz%2C+Haimo%5C%22%22%7D">Ganz, Haimo</a><br />

    <!-- Ganz, Iris --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Ganz%2C+Iris%5C%22%22%7D">Ganz, Iris</a><br />

    <!-- GanzBlum --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22GanzBlum%5C%22%22%7D">GanzBlum</a><br />

    <!-- Gmür, Martina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Gm%5Cu00fcr%2C+Martina%5C%22%22%7D">Gmür, Martina</a><br />

    <!-- Goessi, Markus --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Goessi%2C+Markus%5C%22%22%7D">Goessi, Markus</a><br />

    <!-- Gotovac, Tomislav --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Gotovac%2C+Tomislav%5C%22%22%7D">Gotovac, Tomislav</a><br />

    <!-- Grau, Pascale --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Grau%2C+Pascale%5C%22%22%7D">Grau, Pascale</a><br />

    <!-- Grossenbache, Bettina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Grossenbache%2C+Bettina%5C%22%22%7D">Grossenbache, Bettina</a><br />

    <!-- Gruppe a.b. --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Gruppe+a.b.%5C%22%22%7D">Gruppe a.b.</a><br />

    <!-- Gysin, Lukas --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Gysin%2C+Lukas%5C%22%22%7D">Gysin, Lukas</a><br />

    <!-- Gössi, Markus --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22G%5Cu00f6ssi%2C+Markus%5C%22%22%7D">Gössi, Markus</a><br />

    <!-- Günther, Monika --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22G%5Cu00fcnther%2C+Monika%5C%22%22%7D">Günther, Monika</a><br />

    <!-- Hart, Hanno --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Hart%2C+Hanno%5C%22%22%7D">Hart, Hanno</a><br />

    <!-- Hauert, Sibylle --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Hauert%2C+Sibylle%5C%22%22%7D">Hauert, Sibylle</a><br />

    <!-- Hebendanz, Johannes --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Hebendanz%2C+Johannes%5C%22%22%7D">Hebendanz, Johannes</a><br />

    <!-- Hensler, Markus --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Hensler%2C+Markus%5C%22%22%7D">Hensler, Markus</a><br />

    <!-- Hiepler, Esther --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Hiepler%2C+Esther%5C%22%22%7D">Hiepler, Esther</a><br />

    <!-- Hofer, Marianne --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Hofer%2C+Marianne%5C%22%22%7D">Hofer, Marianne</a><br />

    <!-- Huber, Judith --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Huber%2C+Judith%5C%22%22%7D">Huber, Judith</a><br />

    <!-- Häni, Daniel --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22H%5Cu00e4ni%2C+Daniel%5C%22%22%7D">Häni, Daniel</a><br />

    <!-- Ilić, Aleksandar Battista --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Ili%5Cu0107%2C+Aleksandar+Battista%5C%22%22%7D">Ilić, Aleksandar Battista</a><br />

    <!-- Iselin, Joa --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Iselin%2C+Joa%5C%22%22%7D">Iselin, Joa</a><br />

    <!-- JOKO --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22JOKO%5C%22%22%7D">JOKO</a><br />

    <!-- Jasper, Berndt --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Jasper%2C+Berndt%5C%22%22%7D">Jasper, Berndt</a><br />

    <!-- Jensen, Knut --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Jensen%2C+Knut%5C%22%22%7D">Jensen, Knut</a><br />

    <!-- Johnson, Carla --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Johnson%2C+Carla%5C%22%22%7D">Johnson, Carla</a><br />

    <!-- Josipovic, Mileva --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Josipovic%2C+Mileva%5C%22%22%7D">Josipovic, Mileva</a><br />

    <!-- Jost, Karin --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Jost%2C+Karin%5C%22%22%7D">Jost, Karin</a><br />

    <!-- Jörg, Andrina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22J%5Cu00f6rg%2C+Andrina%5C%22%22%7D">Jörg, Andrina</a><br />

    <!-- Keser, Ivana --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Keser%2C+Ivana%5C%22%22%7D">Keser, Ivana</a><br />

    <!-- Klassen, Norbert --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Klassen%2C+Norbert%5C%22%22%7D">Klassen, Norbert</a><br />

    <!-- Knut & Silvie --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Knut+%26+Silvie%5C%22%22%7D">Knut &amp; Silvie</a><br />

    <!-- Kolb, Lucie --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Kolb%2C+Lucie%5C%22%22%7D">Kolb, Lucie</a><br />

    <!-- Kopp, Regula --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Kopp%2C+Regula%5C%22%22%7D">Kopp, Regula</a><br />

    <!-- Kramer, Samy --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Kramer%2C+Samy%5C%22%22%7D">Kramer, Samy</a><br />

    <!-- Kramer, Sämi --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Kramer%2C+S%5Cu00e4mi%5C%22%22%7D">Kramer, Sämi</a><br />

    <!-- Kraushaar, Seraina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Kraushaar%2C+Seraina%5C%22%22%7D">Kraushaar, Seraina</a><br />

    <!-- Kurz, Simone --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Kurz%2C+Simone%5C%22%22%7D">Kurz, Simone</a><br />

    <!-- Les Reines Prochaines --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Les+Reines+Prochaines%5C%22%22%7D">Les Reines Prochaines</a><br />

    <!-- Lischka, Gerhard Johann --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Lischka%2C+Gerhard+Johann%5C%22%22%7D">Lischka, Gerhard Johann</a><br />

    <!-- Loher, Katja --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Loher%2C+Katja%5C%22%22%7D">Loher, Katja</a><br />

    <!-- Luping, Yue --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Luping%2C+Yue%5C%22%22%7D">Luping, Yue</a><br />

    <!-- Lüber, Heinrich --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22L%5Cu00fcber%2C+Heinrich%5C%22%22%7D">Lüber, Heinrich</a><br />

    <!-- Maag, Irene --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Maag%2C+Irene%5C%22%22%7D">Maag, Irene</a><br />

    <!-- Madörin, Fränzi --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Mad%5Cu00f6rin%2C+Fr%5Cu00e4nzi%5C%22%22%7D">Madörin, Fränzi</a><br />

    <!-- Maly, Valerina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Maly%2C+Valerina%5C%22%22%7D">Maly, Valerina</a><br />

    <!-- Marti, Hansjörg --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Marti%2C+Hansj%5Cu00f6rg%5C%22%22%7D">Marti, Hansjörg</a><br />

    <!-- Mathis, Muda --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Mathis%2C+Muda%5C%22%22%7D">Mathis, Muda</a><br />

    <!-- Moebius, Matthias --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Moebius%2C+Matthias%5C%22%22%7D">Moebius, Matthias</a><br />

    <!-- Morgen, Tony --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Morgen%2C+Tony%5C%22%22%7D">Morgen, Tony</a><br />

    <!-- Müller, Christian --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22M%5Cu00fcller%2C+Christian%5C%22%22%7D">Müller, Christian</a><br />

    <!-- Naegelin, Barbara --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Naegelin%2C+Barbara%5C%22%22%7D">Naegelin, Barbara</a><br />

    <!-- Neecke, Niki --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Neecke%2C+Niki%5C%22%22%7D">Neecke, Niki</a><br />

    <!-- Nieslony, Borys --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Nieslony%2C+Borys%5C%22%22%7D">Nieslony, Borys</a><br />

    <!-- Oechslin, Reto --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Oechslin%2C+Reto%5C%22%22%7D">Oechslin, Reto</a><br />

    <!-- Oertli, Christoph --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Oertli%2C+Christoph%5C%22%22%7D">Oertli, Christoph</a><br />

    <!-- Personnier, Gerald --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Personnier%2C+Gerald%5C%22%22%7D">Personnier, Gerald</a><br />

    <!-- Protoplast --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Protoplast%5C%22%22%7D">Protoplast</a><br />

    <!-- Publikum --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Publikum%5C%22%22%7D">Publikum</a><br />

    <!-- Ranzenhofer, Christoph --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Ranzenhofer%2C+Christoph%5C%22%22%7D">Ranzenhofer, Christoph</a><br />

    <!-- Reichmuth, Daniel --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Reichmuth%2C+Daniel%5C%22%22%7D">Reichmuth, Daniel</a><br />

    <!-- Rerat, Gabriele --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Rerat%2C+Gabriele%5C%22%22%7D">Rerat, Gabriele</a><br />

    <!-- Rickert, Kai --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Rickert%2C+Kai%5C%22%22%7D">Rickert, Kai</a><br />

    <!-- Riedweg, Walter --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Riedweg%2C+Walter%5C%22%22%7D">Riedweg, Walter</a><br />

    <!-- Ritzmann, Marion --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Ritzmann%2C+Marion%5C%22%22%7D">Ritzmann, Marion</a><br />

    <!-- Rodriguez, Sylvie --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Rodriguez%2C+Sylvie%5C%22%22%7D">Rodriguez, Sylvie</a><br />

    <!-- Rust, Dorothea --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Rust%2C+Dorothea%5C%22%22%7D">Rust, Dorothea</a><br />

    <!-- Saemann, Andrea --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Saemann%2C+Andrea%5C%22%22%7D">Saemann, Andrea</a><br />

    <!-- Saner, Clara --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Saner%2C+Clara%5C%22%22%7D">Saner, Clara</a><br />

    <!-- Saraceno, Giovanni --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Saraceno%2C+Giovanni%5C%22%22%7D">Saraceno, Giovanni</a><br />

    <!-- Schechner, Richard --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schechner%2C+Richard%5C%22%22%7D">Schechner, Richard</a><br />

    <!-- Schill, Ruedi --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schill%2C+Ruedi%5C%22%22%7D">Schill, Ruedi</a><br />

    <!-- Schiller, Christoph --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schiller%2C+Christoph%5C%22%22%7D">Schiller, Christoph</a><br />

    <!-- Schilling, Klara --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schilling%2C+Klara%5C%22%22%7D">Schilling, Klara</a><br />

    <!-- Schmid, Regina Florida --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schmid%2C+Regina+Florida%5C%22%22%7D">Schmid, Regina Florida</a><br />

    <!-- Schmidhalter, Hagar --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schmidhalter%2C+Hagar%5C%22%22%7D">Schmidhalter, Hagar</a><br />

    <!-- Schroeder, Celine --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schroeder%2C+Celine%5C%22%22%7D">Schroeder, Celine</a><br />

    <!-- Schuppe, Marianne --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schuppe%2C+Marianne%5C%22%22%7D">Schuppe, Marianne</a><br />

    <!-- Schwabe, Verena --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schwabe%2C+Verena%5C%22%22%7D">Schwabe, Verena</a><br />

    <!-- Schwarzer, Jermias --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schwarzer%2C+Jermias%5C%22%22%7D">Schwarzer, Jermias</a><br />

    <!-- Schweizer & Schweizer --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Schweizer+%26+Schweizer%5C%22%22%7D">Schweizer &amp; Schweizer</a><br />

    <!-- Schüppach, Dina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Sch%5Cu00fcppach%2C+Dina%5C%22%22%7D">Schüppach, Dina</a><br />

    <!-- Schürch, Dorothea --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Sch%5Cu00fcrch%2C+Dorothea%5C%22%22%7D">Schürch, Dorothea</a><br />

    <!-- Sibylle, Hauert --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Sibylle%2C+Hauert%5C%22%22%7D">Sibylle, Hauert</a><br />

    <!-- Sidler, Celia --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Sidler%2C+Celia%5C%22%22%7D">Sidler, Celia</a><br />

    <!-- Sidler, Nathalie --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Sidler%2C+Nathalie%5C%22%22%7D">Sidler, Nathalie</a><br />

    <!-- Siegfried, Walter --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Siegfried%2C+Walter%5C%22%22%7D">Siegfried, Walter</a><br />

    <!-- Signer, Roman --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Signer%2C+Roman%5C%22%22%7D">Signer, Roman</a><br />

    <!-- Silber, Alex --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Silber%2C+Alex%5C%22%22%7D">Silber, Alex</a><br />

    <!-- Spiess, Judith --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Spiess%2C+Judith%5C%22%22%7D">Spiess, Judith</a><br />

    <!-- Studierende HGK Bildhauerklasse --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Studierende+HGK+Bildhauerklasse%5C%22%22%7D">Studierende HGK Bildhauerklasse</a><br />

    <!-- Stäuble, Andreas --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22St%5Cu00e4uble%2C+Andreas%5C%22%22%7D">Stäuble, Andreas</a><br />

    <!-- Tan, Chen --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Tan%2C+Chen%5C%22%22%7D">Tan, Chen</a><br />

    <!-- Videoorchester --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Videoorchester%5C%22%22%7D">Videoorchester</a><br />

    <!-- Weber, Selma --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Weber%2C+Selma%5C%22%22%7D">Weber, Selma</a><br />

    <!-- Weekend Art --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Weekend+Art%5C%22%22%7D">Weekend Art</a><br />

    <!-- Widauer, Nives --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Widauer%2C+Nives%5C%22%22%7D">Widauer, Nives</a><br />

    <!-- Wüsten, Franziska --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22W%5Cu00fcsten%2C+Franziska%5C%22%22%7D">Wüsten, Franziska</a><br />

    <!-- Wüsten, Günther --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22W%5Cu00fcsten%2C+G%5Cu00fcnther%5C%22%22%7D">Wüsten, Günther</a><br />

    <!-- Z'Rotz, Tina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Z%27Rotz%2C+Tina%5C%22%22%7D">Z'Rotz, Tina</a><br />

    <!-- Zwick, Sus --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22Zwick%2C+Sus%5C%22%22%7D">Zwick, Sus</a><br />

    <!-- della Guestina, Christina --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22della+Guestina%2C+Christina%5C%22%22%7D">della Guestina, Christina</a><br />

    <!-- v. Krosigh, Marita --><a href="search.php?query=%7B%22area%22%3A%22%22%2C%22filter%22%3A%5B%5D%2C%22facets%22%3A%7B%22catalog%22%3A%5B%22PCB_Basel%22%5D%7D%2C%22query%22%3A%22author%3A%5C%22v.+Krosigh%2C+Marita%5C%22%22%7D">v. Krosigh, Marita</a><br />


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
