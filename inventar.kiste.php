<?php

$kiste = isset( $_REQUEST['kiste'] ) ? $_REQUEST['kiste'] : null;
if( !$kiste ) return;

function rawToDisplay( $str ) {
	$ret = "";
	$hex = bin2hex( $str );
	$line = 0;
	for( $i = 0; $i < strlen( $str ); $i++ ) {
		if( $i % 4 == 0 ) $ret .= sprintf( "%02d", $line++ ) .": ";
		$ret .= bin2hex( $str{$i}).' ';
		if( $i % 4 == 3 ) {
			$ret .= "&nbsp;&nbsp;";
			for( $j = 3; $j >= 0; $j-- ) { 
				$c = $str{$i-$j};
				if( ord($c) < 32 || ord($c) > 126 ) $ret .= ".";
				else $ret .= htmlentities($c);
			}
			$ret .= "<br />\n";
			
		}
	}
	return "<tt>".$ret."</tt>";
}

?>
<h4 class="small-heading">Kiste <?php echo htmlspecialchars( $kiste ); ?></h3>
	
 <?php
$sql = "SELECT MIN(DATE(inventorytime)) AS start, MAX(DATE(inventorytime)) AS end, COUNT(*) AS num FROM inventory_cache WHERE marker=".$db->qstr($kiste);
echo "<!-- {$sql} -->\n";
$row = $db->GetRow($sql);
echo "<p>Anzahl Medien: {$row['num']}<br />\n";
echo "letzter Inventarisierungszeitraum: {$row['start']}";
if( $row['start'] != $row['end'] ) echo " - {$row['end']}";
echo "</p>\n";

$sql = "SELECT DISTINCT ag.signaturgroup, sg.name FROM inventory_cache, ARC_group ag, signaturgroup sg WHERE id=signaturgroup AND itemid=Strichcode AND marker=".$db->qstr($kiste)." ORDER BY sg.name ASC";
echo "<!-- {$sql} -->\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
	$sql = "SELECT MIN(Signatur) AS start, MAX(Signatur) AS end , COUNT(*) AS num
        FROM inventory_cache i, ARC_group ag, signaturgroup sg
            WHERE ag.signaturgroup={$row['signaturgroup']} AND sg.id=ag.signaturgroup AND itemid=Strichcode AND marker=".$db->qstr($kiste)." GROUP BY ag.signaturgroup";
	echo "<!-- {$sql} -->\n";
	$row2 = $db->GetRow( $sql );
	echo htmlspecialchars( utf8_encode( "{$row2['start']}" ));
	if( $row2['end'] != $row2['start'] ) 
		echo "&nbsp;&nbsp;-&nbsp;&nbsp;".htmlspecialchars( utf8_encode( "{$row2['end']}"))." ({$row2['num']})";
	echo "<br />\n";
}
$rs->Close();
?>
          <table class="table">
            <thead>
              <tr>
                <th>Barcode</th>
                <th>Signatur</th>
                <th>UID</th>
              </tr>
            </thead>
            <tbody>
<?php
$sql = "SELECT i.itemid, signatur, uid, raw FROM inventory i, inventory_cache ic LEFT JOIN ARC ON ic.itemid=Strichcode WHERE i.itemid=ic.itemid AND i.inventorytime=ic.inventorytime AND ic.marker=".$db->qstr($kiste)." ORDER BY itemid ASC, signatur ASC";
echo "<!-- {$sql} -->\n";

$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
echo "<tr>\n";	
echo "  <td><a href=\"http://opac.nebis.ch/F?local_base=nebis&func=find-c&ccl_term=".urlencode("BAR=".$row['itemid'])."\">".htmlspecialchars($row['itemid'])."</a></td>\n";
echo "  <td><a href=\"http://opac.nebis.ch/F?local_base=nebis&func=find-c&ccl_term=".urlencode("WLB=".$row['signatur'])."\">".htmlspecialchars($row['signatur'])."</a></td>\n";
echo "  <td><button type=\"button\" class=\"btn btn-default\" data-container=\"body\" data-toggle=\"popover\" data-placement=\"top\" data-content=\"".rawToDisplay($row['raw'])."\">
  ".htmlspecialchars($row['uid'])."
</button></td>\n";
echo "</tr>\n";	
}
$rs->Close();

?>			
            </tbody>
          </table>
