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

if( !$session->isLoggedIn()) {
	header( 'Location: auth/?target='.urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
}

echo mediathekheader('gatekeeper', 'Mediathek - Gatekeeper', '', array( 'css/jquery.dynatable.css' ));
?>

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix full-height">
                    <h2 class="small-heading">Gatekeeper</h2>
					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
if( $session->inGroup( 'global/admin' )) {
?>
<!-- <h3>Gatekeeper</h3> -->

<div id="faq" role="tablist" aria-multiselectable="true">

  <div class="card">
    <div class="card-header" role="tab" id="eventsH">
      <h5 class="card-title">
        <a class="collapsed" data-toggle="collapse" data-parent="#faq" href="#events" aria-expanded="true" aria-controls="events">
        Events
        </a>
      </h5>
    </div>
    <div id="events" class="collapse in" role="tabcard" aria-labelledby="eventsH">
      <div class="card-block">
        <table id="eventtable" class="table">
          <thead>
            <tr>
              <th>Time</th>
              <th>Gate</th>
              <th>UID</th>
              <th>AFI</th>
              <th>Status</th>
              <th>Item</th>
              <th>ISIL</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<div class="card">
  <div class="card-header" role="tab" id="hourGateH">
    <h5 class="card-title">
      <a data-toggle="collapse" data-parent="#faq" href="#hourGate" aria-expanded="true" aria-controls="hourGate">
      Counter Stundenansicht <?php echo date( 'd.m.Y' ); ?>
      </a>
    </h5>
  </div>
  <div id="hourGate" class="collapse in" role="tabcard" aria-labelledby="hourGateH">
    <div class="card-block">
				<div id="hourchart" style="width: 900px; height: 300px;"></div>
				<div id="hourchart_n" style="width: 900px; height: 300px;"></div>
				<div id="hourchart_s" style="width: 900px; height: 300px;"></div>
    <script>
    </script>
    </div>
  </div>
</div>


<div class="card">
  <div class="card-header" role="tab" id="dayChartH">
    <h5 class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#faq" href="#dayChart" aria-expanded="false" aria-controls="dayChart">
      Counter Letzte 10 Tage
      </a>
    </h5>
  </div>
  <div id="dayChart" class="collapse" role="tabcard" aria-labelledby="dayChartH">
    <div class="card-block">
			<div id="daychart" style="width: 900px; height: 300px;"></div>
			<div id="daychart_n" style="width: 900px; height: 300px;"></div>
			<div id="daychart_s" style="width: 900px; height: 300px;"></div>
    </div>
  </div>
</div>

</div>


<?php  } else { ?>
<h4>Unzureichende Berechtigung</h4>
Bitte <a href="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>">anmelden</a>
<?php } ?>

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

function drawHourChart() {

  $.getJSON( "gatekeeper_data.php?type=hour&day=<?php echo date( 'Y-m-d' ); ?>",
    function ( json ) {
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);
    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('hourchart'));
    chart.draw(data, {
			title:'Northgate & Southgate',
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
     });

    });
}

function drawHourChart_n() {

  $.getJSON( "gatekeeper_data.php?gate=north&type=hour&day=<?php echo date( 'Y-m-d' ); ?>",
    function ( json ) {
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);
    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('hourchart_n'));
    chart.draw(data, {
			title:'Northgate',
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
     });

    });
}

function drawHourChart_s() {

  $.getJSON( "gatekeeper_data.php?gate=south&type=hour&day=<?php echo date( 'Y-m-d' ); ?>",
    function ( json ) {
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);
    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('hourchart_s'));
    chart.draw(data, {
			title:'Southgate',
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
     });

    });
}

function drawWeekChart() {

  $.getJSON( "gatekeeper_data.php?type=week&week=<?php echo date( 'W' ); ?>&year=<?php echo date( 'Y' ); ?>",
    function ( json ) {

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);


    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('weekchart'));
    chart.draw(data, {
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
     });

    });
}

function drawDayChart() {
<?php
  $d1 = new \DateTime();
  $d2 = new \DateTime();
  $d2->modify( '-10 days' );
?>
  $.getJSON( "gatekeeper_data.php?type=days&begin=<?php echo $d2->format('Y-m-d'); ?>&end=<?php echo $d1->format('Y-m-d'); ?>",
    function ( json ) {

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);


    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('daychart'));
    chart.draw(data, {
			title: 'Northgate & Southgate',
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
       hAxis: {
        slantedText: true
      }
     });

    });
}

function drawDayChart_n() {
<?php
  $d1 = new \DateTime();
  $d2 = new \DateTime();
  $d2->modify( '-10 days' );
?>
  $.getJSON( "gatekeeper_data.php?gate=north&type=days&begin=<?php echo $d2->format('Y-m-d'); ?>&end=<?php echo $d1->format('Y-m-d'); ?>",
    function ( json ) {

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);


    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('daychart_n'));
    chart.draw(data, {
			title: 'Northgate',
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
       hAxis: {
        slantedText: true
      }
     });

    });
}

function drawDayChart_s() {
<?php
  $d1 = new \DateTime();
  $d2 = new \DateTime();
  $d2->modify( '-10 days' );
?>
  $.getJSON( "gatekeeper_data.php?gate=south&type=days&begin=<?php echo $d2->format('Y-m-d'); ?>&end=<?php echo $d1->format('Y-m-d'); ?>",
    function ( json ) {

    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.arrayToDataTable(json);


    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('daychart_s'));
    chart.draw(data, {
			title: 'Southgate',
       width: 900,
       height: 300,
       legend: { position: 'none', maxLines: 3 },
       bar: { groupWidth: '75%' },
       isStacked: true,
       hAxis: {
        slantedText: true
      }
     });

    });
}

function init() {

		$('body').on('click', '.back-btn', function () {
		window.location="search.php";
	});

  google.charts.load("current", {'packages':['corechart', 'bar']});
	google.charts.setOnLoadCallback(drawHourChart);
	google.charts.setOnLoadCallback(drawHourChart_n);
	google.charts.setOnLoadCallback(drawHourChart_s);
  //google.charts.setOnLoadCallback(drawWeekChart);
	google.charts.setOnLoadCallback(drawDayChart);
	google.charts.setOnLoadCallback(drawDayChart_n);
	google.charts.setOnLoadCallback(drawDayChart_s);

  $('#eventtable').dynatable({
		dataset: {
			ajax: true,
			ajaxUrl: 'gatekeeper_event.php',
			ajaxOnLoad: true,
			records: []
		},
		table: {
			defaultColumnIdStyle: 'camelCase',
			columns: null,
			headRowSelector: 'thead tr', // or e.g. tr:first-child
			bodyRowSelector: 'tbody tr',
			headRowClass: null
		},
	});


}
</script>


<?php
echo mediathekfooter( array( "https://www.gstatic.com/charts/loader.js", "js/jquery.dynatable.js" ));
?>
