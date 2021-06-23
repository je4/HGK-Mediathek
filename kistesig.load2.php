<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

include 'config.inc.php';

require_once 'lib/adodb5/adodb-exceptions.inc.php';
require_once 'lib/adodb5/adodb.inc.php';

$db = null;

function doConnectMySQL($force = false) {
    global $db, $config;
    if( $db == null ) {
        $db = NewAdoConnection( 'mysqli2' );
    }
    if( $force || !$db->isConnected()) {
        try {
            if( !$db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] )) {
                sleep( 1 );
                $db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
            }
        }
        catch( Exception $e ) {
            sleep( 1 );
            try {
              $db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
            }
            catch( Exception $e ) {
              print_r( $config['db']);
              throw $e;

            }
        }
        $db->SetCharSet('utf8');
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
    }
}

doConnectMySQL();

$signatur = null;
if( isset( $_REQUEST['signatur'] )) $signatur = trim( ($_REQUEST['signatur']));

if( !strlen( $signatur ) ) return;

$sql = "SELECT DISTINCT a.`Signatur` AS signatur, i.itemid, i.marker
			FROM inventory_cache i LEFT JOIN ARC a ON a.`Strichcode`=i.itemid
			WHERE (a.`Signatur` LIKE ".$db->qstr("%{$signatur}%")."
			OR i.itemid LIKE ".$db->qstr("{$signatur}%")."
			OR i.marker LIKE ".$db->qstr("{$signatur}%").")
			ORDER BY a.`Signatur` ASC LIMIT 0, 600";
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
		$boxjson = null;
		$kbox = str_replace( '_', '', $box );
		if( file_exists( $config['3djsondir']."/{$kbox}.json" )) {
			$boxjson = file_get_contents( $config['3djsondir']."/{$kbox}.json" );
		}
		echo '<td class="list" style="width: 30%;"><a href="#">'.$box.'</a>
				<a style="padding: 0px;" href="#" class="btn btn-default" data-toggle="modal" data-target="#MTModal" data-kiste="'.urlencode(str_replace( '_', '', $box)).'" data-json="'.htmlspecialchars( $boxjson, ENT_QUOTES ).'"> <i class="fa fa-street-view" aria-hidden="true"></i></a>'."</td>\n";
	echo '<td class="list" style="width: 30%;">'.htmlspecialchars(/* utf8_encode */($row['signatur']))."</td>\n";
	echo '<td class="list" style="width: 40%;">'.htmlspecialchars(utf8_encode($row['itemid']))."</td>\n";
	echo "</tr>\n";
}
$rs->close();
?>
</table>
