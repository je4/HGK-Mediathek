<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

if( !$session->isLoggedIn()) {
	header( 'Location: auth/?target='.urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
}

// get request values
$new = isset( $_REQUEST['new'] ) ? $_REQUEST['new'] : null; 
$online = isset( $_REQUEST['online'] ) ? intval( $_REQUEST['online'] ) > 0 : null;
$network = isset( $_REQUEST['network'] ) ? $_REQUEST['network'] : null;
$lib = isset( $_REQUEST['lib'] ) ? $_REQUEST['lib'] : null;

echo mediathekheader('search', 'Swissbib - Redundanzcheck - '.($lib ? $lib : ''), '');
?>
<div class="back-btn"><i class="ion-ios-search"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings-strong': 'ion-log-in'; ?>"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-1 col-sm-12">
                <div class="mask">
                </div>
                <div style="left: 20%;" class="main-heading">
                    <h1>Redundancy</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix full-height">

		<h2 class="small-heading">Mediathek der Künste</h2>
	
		<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php 
if( $network == null ) {
?>
<center>
<table class="table table-striped" style="width: auto;">
<thead>
	<tr>
		<th>Verbund</th>
		<th>Items</th>
		<th>Items (new)</th>
	</tr>
</thead>
<tbody>
<?php 	
	$sql = "SELECT network, SUM(items) AS count, SUM(items_new) AS count_new FROM stats_swissbib_libs GROUP BY network ORDER BY network ASC";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
?>
	<tr>
		<td><a href="?network=<?php echo urlencode( $row['network']); ?>"><?php echo htmlspecialchars( $row['network'] ); ?></a></td>
		<td style="text-align: right;"><?php echo number_format( $row['count'], 0, '.', "'" ); ?></td>
		<td style="text-align: right;"><?php echo number_format( $row['count_new'], 0, '.', "'" ); ?></td>
	</tr>
<?php 		
	}
	$rs->Close();
?>
</tbody>
</table>
</center>
<?php 	
} 
elseif( $lib == null ) {
?>
	<center>
	<h3><?php echo htmlspecialchars( $network ); ?></h3>
	<table class="table table-striped" style="width: auto;">
	<thead>
		<tr>
			<th>Kürzel</th>
			<th>Name</th>
			<th>Items</th>
			<th>Items (new)</th>
		</tr>
	</thead>
	<tbody>
<?php 	
	$sql = "SELECT * FROM stats_swissbib_libs WHERE items > 1 AND network=".$db->qstr( $network );
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		?>
		<tr>
			<td><a href="?network=<?php echo urlencode( $network); ?>&lib=<?php echo urlencode( $row['libcode']); ?>"><?php echo htmlspecialchars( $row['libcode'] ); ?></a></td>
			<td><a href="?network=<?php echo urlencode( $network); ?>&lib=<?php echo urlencode( $row['libcode']); ?>"><?php echo htmlspecialchars( $row['fullname'] ); ?></a></td>
			<td><?php echo number_format( $row['items'], 0, '.', "'" ); ?></td>
			<td><?php echo number_format( $row['items_new'], 0, '.', "'" ); ?></td>
		</tr>
	<?php 		
	}
	$rs->Close();
?>
	</tbody>
	</table>
<?php 	
}
elseif( $network != null && $lib != null ) {
	$sql = "SELECT fullname FROM stats_swissbib_libs WHERE libcode=".$db->qstr( $lib );
	$fullname = $db->GetOne( $sql );
	$sql = "SELECT SUM(items) AS items FROM stats_swissbib_types WHERE network=".$db->qstr( $network )." AND libcode=".$db->qstr( $lib );
	if( $online !== null ) $sql .= " AND online=".($online?'true':'false');
	$items = intval( $db->GetOne( $sql ));
	$sql = "SELECT SUM(items) AS items FROM stats_swissbib_types WHERE new=true AND network=".$db->qstr( $network )." AND libcode=".$db->qstr( $lib );
	if( $online !== null ) $sql .= " AND online=".($online?'true':'false');
	$newitems = intval( $db->GetOne( $sql ));
	?>
<center>
<style>
.btn-selected {
	color: #373a3c; background-color: #e6e6e6;border-color: #adadad;
}
</style>
	<h3><?php echo htmlspecialchars( $network.':'.$lib.(strlen( $fullname ) ? ' - '.$fullname : '')); ?></h3>
<?php
	$squery = $solrclient->createSelect();
	$helper = $squery->getHelper();
	$squery->setRows( 10 );
	$q = "deleted:false AND source:swissbib AND category:".$helper->escapePhrase( "2!!signature!!{$network}!!{$lib}" );
	//echo "{$q}<br />\n";
	$squery->createFilterQuery('q')->setQuery($q);
	$q = "year:[".(intval(date('Y'))-4)." TO *]";
	//echo "	{$q}<br />\n";
	$squery->createFilterQuery('new')->addTag('new')->setQuery($q);
	if( $online !== null ) {
		$q = "online:".($online?'true':'false');
		//echo "{$q}<br />\n";
		$squery->createFilterQuery('online')->addTag('online')->setQuery($q);
	}
	$facetSetCatalog = $squery->getFacetSet();
	$facetSetCatalog->createFacetField('category')->setField('category')->setPrefix('2!!signature!!')->setLimit( 2000 )->setMinCount(1);
	$facetSetType = $squery->getFacetSet();
	$squery->setQuery( "*:*" );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	$newlist = array();
	$facetCategory = $rs->getFacetSet()->getFacet('category');
	foreach( $facetCategory as $category => $count ) {
		$newlist[$category] = $count;
	}

	$squery = $solrclient->createSelect();
	$helper = $squery->getHelper();
	$squery->setRows( 10 );
	$q = "deleted:false AND source:swissbib AND category:".$helper->escapePhrase( "2!!signature!!{$network}!!{$lib}" );
	//echo "{$q}<br />\n";
	$squery->createFilterQuery('q')->setQuery($q);
	if( $new !== null ) {
		$q = "year:[".(intval(date('Y'))-4)." TO *]";
		//echo "	{$q}<br />\n";
		$squery->createFilterQuery('new')->addTag('new')->setQuery($q);
	}
	if( $online !== null ) {
		$q = "online:".($online?'true':'false');
		echo "{$q}<br />\n";
		$squery->createFilterQuery('online')->addTag('online')->setQuery($q);
	}
	$facetSetCatalog = $squery->getFacetSet();
	$facetSetCatalog->createFacetField('category')->setField('category')->setPrefix('2!!signature!!')->setLimit( 2000 )->setMinCount(1);
	$facetSetType = $squery->getFacetSet();
	$facetSetType->createFacetField('type')->setField('type')->setLimit( 100 )->setMinCount(1)->addExclude('type');
	$facetSetOnline = $squery->getFacetSet();
	$facetSetOnline->createFacetField('online')->setField('online')->setLimit( 10 )->setMinCount(1)->addExclude('online');
	$squery->setQuery( "*:*" );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	
	
/*
<button type="button" class="btn btn-outline-secondary">Secondary</button>
*/
	$facetType = $rs->getFacetSet()->getFacet( 'type' );
	foreach( $facetType as $t => $count ) {
?>
		<?php echo htmlentities( "{$t} (".number_format( $count, 0, '.', "'" ).")" ); ?>;
<?php 
	}
	
	$facetCategory = $rs->getFacetSet()->getFacet('category');
?>
	<p>&nbsp;</p>
	<table class="table table-striped" style="width: auto;">
	<thead>
<?php 
	ob_start();
?>
		<tr>
			<th>Verbund</th>
			<th>Kürzel</th>
			<th>Name</th>
			<th>Redundante Items</th>
			<th>Red. Value</th>
			<th>Redundante Items (new)</th>
			<th>Red. Value (new)</th>
		</tr>
	</thead>
	<tbody>
<?php 	
	$i = 0;
	$sum = 0;
	$sum_ext = 0;
	foreach( $facetCategory as $category => $count ) {
		$i++;
		$newcount = 0;
		if( array_key_exists( $category, $newlist )) $newcount = $newlist[$category];
		if(preg_match( "/2!!signature!!(.+)!!(.+)$/", $category, $matches )) {
		$net = $matches[1];
		$libcode = $matches[2];
		$sql = "SELECT fullname FROM stats_swissbib_libs WHERE libcode=".$db->qstr( $libcode )." AND network=".$db->qstr( $net );
		$full = $db->GetOne( $sql );
		$sql = "SELECT SUM(items) AS items FROM stats_swissbib_types WHERE network=".$db->qstr( $net )." AND libcode=".$db->qstr( $libcode );
		if( $online !== null ) $sql .= " AND online=".($online?'true':'false');
		$items_ext = intval( $db->GetOne( $sql ));
		$sql = "SELECT SUM(items) AS items FROM stats_swissbib_types WHERE new=true AND network=".$db->qstr( $net )." AND libcode=".$db->qstr( $libcode );
		if( $online !== null ) $sql .= " AND online=".($online?'true':'false');
		$newitems_ext = intval( $db->GetOne( $sql ));		
		$percent = round( $count*100/$items);
		$percent_ext = round( $count*100/$items_ext);
		$newpercent = $newitems > 0 ? round( $newcount*100/$newitems) : 0;
		$newpercent_ext = $newitems_ext > 0 ? round( $newcount*100/$newitems_ext) : 0;
		$redval = $percent*$percent_ext/(100*100);
		$sum += $redval;
		$redval_ext = $newpercent*$newpercent_ext/(100*100);
		$sum_ext += $redval_ext;
		if( $percent == 0 && $i > 10 ) continue;
?>
		<tr>
			<td><?php echo htmlspecialchars( $net ); ?></td>
			<td><a href="<?php echo '?network='.urlencode( $net); echo '&lib='.urlencode( $libcode); if( $online !== null ) if( $online !== null ) echo '&online='.urlencode( $online ); ?>"><?php echo htmlspecialchars( $libcode ); ?></a></td>
			<td><?php echo htmlspecialchars( $full ); ?></td>
			<td style="text-align: right; background-image:url('img/A0B6FF.png'); background-repeat: no-repeat; background-position: left top; background-size: <?php echo $percent; ?>% 100%;">
				<?php echo number_format( $count, 0, '.', "'" ); ?>
			</td>
			<td style="text-align: right;"><?php echo "{$percent}% * {$percent_ext}% = "; printf( '%.3f', $redval); ?></td>
			<td  style="text-align: right; background-image:url('img/A0B6FF.png'); background-repeat: no-repeat; background-position: left top; background-size: <?php echo $newpercent; ?>% 100%;">
				<?php echo number_format( $newcount, 0, '.', "'" ); ?>
			</td>
			<td style="text-align: right;"><?php echo "{$newpercent}% * {$newpercent_ext}% = "; printf( '%.3f', $redval_ext); ?></td>
		</tr>
<?php
		}
	}
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td style="text-align: right;"><b><?php printf( '%.3f', $sum ); ?></b></td>
			<td>&nbsp;</td>
			<td style="text-align: right;"><b><?php printf( '%.3f', $sum_ext ); ?></b></td>
		</tr>
	</tbody>
	</table>
<?php 
	$out = ob_get_clean();
?>
		<tr>
			<th style="text-align: right;" colspan="4">Redundanzwert</th>
			<th style="color: red;"><?php printf( '%.3f', $sum ); ?></th>
			<th>Redundanzwert (new)</th>
			<th style="color: red;"><?php printf( '%.3f', $sum_ext ); ?></th>
		</tr>
<?php echo $out; ?>			
</center>
<?php 	
}
?>		
	</tbody>
	</table>
	</center>	
			</div>
		</div>
	</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Copyright &copy; 2016
							<a href="#">Mediathek der Künste</a>
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
<script>

function init() {
	
	$('body').on('click', '.setting-btn', function () {
		<?php if( $session->isLoggedIn()) { ?>
		window.location="settings.php";
		<?php } else { ?>
		window.location="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>";
		<?php } ?>
		
	});

	$('body').on('click', '.back-btn', function () {
		window.location="index.php";
	});
	
<?php
	if( isset( $doc )) echo "init{$doc->source}();";
?>
	
}
</script>   

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>
<script src="js/threex.domresize.js"></script>

<?php
echo mediathekfooter();
?>

  
