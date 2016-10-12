<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

global $db;

function getDescription( $reihe ) {
	global $db, $config;
	
	$js_sourcelist = '';
	$ds = $config['defaultsource'];
	$ds[] = 'NEBIS';
	$ds = array_unique( $ds );
	foreach( $ds as $src )
		$js_sourcelist .= ",'".trim( $src )."'";
	$js_sourcelist = trim( $js_sourcelist, ',');
	
	ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
	
?>
<h4 class="small-heading">
<a href="javascript:doSearchFull('location:&quot;<?php echo 'E75:Kiste:'.substr_replace( trim( $reihe ), '?', 4, 1 ); ?>&quot;', '', [], {'source':[<?php echo $js_sourcelist; ?>]}, 0, 25 );">
<?php echo htmlspecialchars( $reihe ); ?>
</a>
</h4>
<?php
	
	$sql = "SELECT MIN(DATE(inventorytime)) AS start, MAX(DATE(inventorytime)) AS end, COUNT(*) AS num FROM inventory_cache WHERE marker LIKE ".$db->qstr(str_replace("00", "__", $reihe));
	echo "<!-- {$sql} -->\n";
	$row = $db->GetRow($sql);
	echo "<p>Anzahl Medien: {$row['num']}<br />\n";
	echo "letzter Inventarisierungszeitraum: {$row['start']}";
	if( $row['start'] != $row['end'] ) echo " - {$row['end']}";
	echo "</p>\n";
	$sql = "SELECT DISTINCT signaturgroup, signaturgroup.name, signaturgroup.descr, signaturgroup.signatursys
			FROM inventory_cache, ARC_group, signaturgroup
			WHERE id=signaturgroup AND itemid=Strichcode AND marker LIKE ".$db->qstr(str_replace("00", "__", $reihe))."
			ORDER BY signaturgroup.name ASC";
	echo "<!-- {$sql} -->\n";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		$sql = "SELECT MIN(signatur) AS start, MAX(signatur) AS end , COUNT(*) AS num
		FROM inventory_cache	, ARC_group, signaturgroup
		WHERE signaturgroup={$row['signaturgroup']} AND id=signaturgroup AND itemid=Strichcode AND marker  LIKE ".$db->qstr(str_replace("00", "__", $reihe))." GROUP BY signaturgroup";
		echo "<!-- {$sql} -->\n";
		$row2 = $db->GetRow( $sql );
		$txt = '';
		if( strlen( $row['descr'])) {
			$txt .= htmlspecialchars( $row['name'] )." - ".htmlspecialchars( $row['descr'] )."";
			
		}
		if( $row2['end'] != $row2['start'] ) {
			$txt .= (strlen( $row['descr']) ? ' // ' : '' ).htmlspecialchars( utf8_encode( "{$row2['end']}"))." ({$row2['num']})";
		}
		if( strlen( trim( $txt )))
			echo $txt."<br />\n";
	}
	$rs->Close();
	
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
}

// get request values
$func = 'reihe';
$qr = isset( $_REQUEST['reihe'] ) ? $_REQUEST['reihe'] : null;


echo mediathekheader('reihe', 'Mediathek - Reihe '.$qr, '');
?>
<div class="back-btn"><i class="ion-android-arrow-back"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings-strong': 'ion-log-in'; ?>"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
		<!--( a ) Profile Page Fixed Image Portion -->

		<div class="image-container col-md-1 col-sm-12">
			<div class="mask">
			</div>
			<div style="left: 20%;" class="main-heading">
				<h1>Reihe</h1>
			</div>
		</div>

<?php
if( preg_match( '/([A-Za-z])_([0-9]{3})_([a-z])/', $qr, $matches )) {
	$ra = $matches[1].'_'.$matches[2].'_a';
	$rb = $matches[1].'_'.$matches[2].'_b';
	$side = $matches[3];
?>
		<div class="content-container col-md-offset-1 col-md-9 col-sm-12">
			<div class="clearfix">
				<h2 class="small-heading">Reihe</h2>
				<?php if( $side == 'a' ) { ?>
				<div><?php echo getDescription( $ra ); ?></div>
				<div style="width:100%; text-align: right;"><?php echo getDescription( $rb ); ?></div>
				<?php } else { ?>
				<div style="width:100%; text-align: right;"><?php echo getDescription( $ra ); ?></div>
				<div><?php echo getDescription( $rb ); ?></div>
				<?php } ?>
			</div>
		</div>

<?php } ?>
	</div>
</div>
<script>

function init() {


	$('body').on('click', '.back-btn', function () {
		window.location="index.php";
	});
	
	
}
</script>   
<script src="js/tether.min.js"></script>   

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>

<?php
echo mediathekfooter();
?>

  
