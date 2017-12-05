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

echo mediathekheader('gatekeeper', 'Videowochen im Wenkenpark', '', array( '../css/dataTables.bootstrap4.min.css' ));
?>

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
  <div class="clearfix full-height">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                    <h2 class="small-heading">Videowochen im Wenkenpark</h2>
                  </div>
      </div>
      <div class="row">
      <div class="col-md-3">&nbsp;</div>
      <div class="col-md-6">

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<p><center><img src="img/VWW_Coverbild.jpg" style="width: 80%" /></center></p>
<h4>Videowochen im Wenkenpark (1984 / 1986 / 1988)</h4>
<p>
Die Videowochen im Wenkenpark bildeten in den achziger Jahren ein
internationales Forum für die Videokunst, das mit den Schwerpunkten
Produktion, Ausbildung, Vermittlung  und Präsentation von Videoarbeiten,
Installationen sowie Performances, den damaligen Bedürfnissen entsprach.
Die Veranstaltungen setzten massgebliche Impulse und trugen zur Etablierung
des Mediums im Kunstkontext bei.
</p>

<p>
  <form class="form-inline" method=GET action="search.php">
    <input type="hidden" name="facets[catalog][]" value="Wenkenpark" />
    <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="inlineFormInput" placeholder="Suchbegriff" name="search">
    <button type="submit" class="btn btn-primary">Suchen</button>
  </form>
</p>

<p>
Im Rahmen der internationalen Videowochen im Wenkenpark, die 1984, 1986 und 1988
stattfanden, wurden Videoproduktionen mit eingeladenen KünstlerInnen in
unterschiedlichen Workshops realisiert. Videodokumentationen von Video-Live-Auftritten,
Workshop-Prozessen und 5 ausführliche Interviews mit eingeladenen KünstlerInnen ergänzen
die Sammlung der Produktionen der Videowochen.
</p>
<p>
  <table border=0 style="width: 100%;">
    <tr>
      <td style="width: 33%; vertical-align: top;">
        <h4>1984</h4>
<?php
  $query =   array (
      'area' => '',
      'filter' => array (),
      'facets' => array (
          'catalog' => array (
            0 => 'Wenkenpark',
          ),
          'cluster' => array (
            0 => 'Videowoche im Wenkenpark 1984',
          ),
      ),
      'query' => 'author:"Bielz,+Gudrun"',
  );

  $names = array();
  $thema = '1984';
  $sql = "SELECT DISTINCT name, THEMA FROM source_wenkenpark_people WHERE THEMA='{$thema}' ORDER BY name ASC;";
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    $name = $row['name'];
    foreach( explode( ';', $name ) as $n ) {
      $names[] = trim(preg_replace( '/\(.+\)/', '', $n ));
    }
  }
  $rs->Close();
  sort( $names );
  $names = array_unique( $names );
  foreach( $names as $name ) {
    $query['query'] = 'author:"'.$name.'"';
    $query['facets']['cluster'] = array( 'Videowoche im Wenkenpark '.$thema );
    echo "<!-- {$row['name']} --><a href=\"search.php?query=".urlencode( json_encode( $query ))."\">".htmlspecialchars( $name )."</a><br />\n";
  }
?>
      </td>
      <td style="width: 33%; vertical-align: top;">
        <h4>1986</h4>
        <?php
        $names = array();
        $thema = '1986';
        $sql = "SELECT DISTINCT name, THEMA FROM source_wenkenpark_people WHERE THEMA='{$thema}' ORDER BY name ASC;";
        $rs = $db->Execute( $sql );
        foreach( $rs as $row ) {
          $name = $row['name'];
          foreach( explode( ';', $name ) as $n ) {
            $names[] = trim(preg_replace( '/\(.+\)/', '', $n ));
          }
        }
        $rs->Close();
        sort( $names );
        $names = array_unique( $names );
        foreach( $names as $name ) {
          $query['query'] = 'author:"'.$name.'"';
          $query['facets']['cluster'] = array( 'Videowoche im Wenkenpark '.$thema );
          echo "<!-- {$row['name']} --><a href=\"search.php?query=".urlencode( json_encode( $query ))."\">".htmlspecialchars( $name )."</a><br />\n";
        }
        ?>
      </td>
      <td style="width: 33%; vertical-align: top;">
        <h4>1988</h4>
        <?php
        $names = array();
        $thema = '1988';
        $sql = "SELECT DISTINCT name, THEMA FROM source_wenkenpark_people WHERE THEMA='{$thema}' ORDER BY name ASC;";
        $rs = $db->Execute( $sql );
        foreach( $rs as $row ) {
          $name = $row['name'];
          foreach( explode( ';', $name ) as $n ) {
            $names[] = trim(preg_replace( '/\(.+\)/', '', $n ));
          }
        }
        $rs->Close();
        sort( $names );
        $names = array_unique( $names );
        foreach( $names as $name ) {
          $query['query'] = 'author:"'.$name.'"';
          $query['facets']['cluster'] = array( 'Videowoche im Wenkenpark '.$thema );
          echo "<!-- {$row['name']} --><a href=\"search.php?query=".urlencode( json_encode( $query ))."\">".htmlspecialchars( $name )."</a><br />\n";
        }
        ?>
      </td>
    </tr>
  </table>
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
