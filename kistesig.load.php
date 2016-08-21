<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include( 'init.inc.php' );

$signatur = null;
if( isset( $_REQUEST['signatur'] )) $signatur = trim( ($_REQUEST['signatur']));

if( !strlen( $signatur ) ) return;

$canEdit = $session->inGroup( 'mediathek/inventory' );

$sql = "SELECT DISTINCT a.`Signatur` AS signatur, i.itemid, i.marker 
			FROM inventory_cache i LEFT JOIN ARC a ON a.`Strichcode`=i.itemid
			WHERE (a.`Signatur` LIKE ".$db->qstr("%{$signatur}%")."
			OR i.itemid LIKE ".$db->qstr("{$signatur}%")."
			OR i.marker LIKE ".$db->qstr("{$signatur}%").")
			ORDER BY a.`Signatur` ASC LIMIT 0, 50";
//echo "<--\n {$sql}\n -->\n";
$rs = $db->Execute( $sql );			
?>
<table class="table" style="table-layout: fixed;">
	<tr>
		<th class="list" style="width: 30%;">Standort</th>
		<th class="list" style="width: 30%;">Signatur</th>
		<th class="list" style="width: 40%;">Barcode</th>
	</tr>
<?php
foreach( $rs as $row ) {
	$box = $row['marker'];
	echo "<tr>\n";  // E75:Kiste
	if( $canEdit ) {
		echo '<td class="list" style="width: 30%;"><a href="#" data-pk="'.$row['itemid'].'" data-type="text" data-url="inventory.update.php" data-name="marker" id="'.$row['itemid'].'" class="x-editable" )">'.$box."</a></td>\n";
	}
	else {
		echo '<td class="list" style="width: 30%;"><a href="#" onClick="doSearchFull( \'location:E75:Kiste:'.$box.'\', \'\', [], [], 0, '.$session->getPageSize().' )">'.$box.'</a> <a style="padding: 0px;" href="#" class="btn btn-default" data-toggle="modal" data-target="#MTModal" data-kiste="'.urlencode(str_replace( '_', '', $box)).'" > <i class="fa fa-street-view" aria-hidden="true"></i></a>'."</td>\n";
	}
	echo '<td class="list" style="width: 30%;">'.htmlspecialchars(utf8_encode($row['signatur']))."</td>\n";
	echo '<td class="list" style="width: 40%;">'.htmlspecialchars(utf8_encode($row['itemid']))."</td>\n";
	echo "</tr>\n";
}
$rs->close();
?>	
</table>
<script>
	var trigger = $("body").find('[data-toggle="modal"]');
	trigger.click(function() {
		var theModal = $(this).data( "target" );
		src3d = $(this).attr( "data-3D" ); 
		$(theModal+' iframe').attr('src', src3d);
		$(theModal+' button.close').click(function () {
			$(theModal+' iframe').attr('src', src3d);
		});   
	});
	
	$.fn.editable.defaults.mode = 'inline';
	$('.x-editable').editable( { showbuttons: false });
</script>
