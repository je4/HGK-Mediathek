<?php

$reihe = isset( $_REQUEST['reihe'] ) ? $_REQUEST['reihe'] : null;
if( !$reihe ) return;

?>
<h4 class="small-heading">Reihe <?php echo htmlspecialchars( $reihe ); ?></h3>
	
 <?php
$sql = "SELECT MIN(DATE(inventorytime)) AS start, MAX(DATE(inventorytime)) AS end, COUNT(*) AS num FROM inventory2 WHERE marker LIKE ".$db->qstr(str_replace("00", "__", $reihe));
echo "<!-- {$sql} -->\n";
$row = $db->GetRow($sql);
echo "<p>Anzahl Medien: {$row['num']}<br />\n";
echo "letzter Inventarisierungszeitraum: {$row['start']}";
if( $row['start'] != $row['end'] ) echo " - {$row['end']}";
echo "</p>\n";
$sql = "SELECT DISTINCT signaturgroup, signaturgroup.name, signaturgroup.descr, signaturgroup.signatursys
        FROM inventory2, ARC_group, signaturgroup
        WHERE id=signaturgroup AND itemid=Strichcode AND marker LIKE ".$db->qstr(str_replace("00", "__", $reihe))."
        ORDER BY signaturgroup.name ASC";
echo "<!-- {$sql} -->\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
	$sql = "SELECT MIN(signatur) AS start, MAX(signatur) AS end , COUNT(*) AS num
    FROM inventory2, ARC_group, signaturgroup
    WHERE signaturgroup={$row['signaturgroup']} AND id=signaturgroup AND itemid=Strichcode AND marker  LIKE ".$db->qstr(str_replace("00", "__", $reihe))." GROUP BY signaturgroup";
	echo "<!-- {$sql} -->\n";
	$row2 = $db->GetRow( $sql );
echo htmlspecialchars( utf8_encode( "[{$row['signaturgroup']}] {$row2['start']}" ));
	if( $row2['end'] != $row2['start'] ) 
		echo "&nbsp;&nbsp;-&nbsp;&nbsp;".htmlspecialchars( utf8_encode( "{$row2['end']}"))." ({$row2['num']})";
	if( strlen( $row['descr']))
		echo " // ".htmlspecialchars( $row['name'] )." - ".htmlspecialchars( $row['descr'] )."";
	else
		echo " // {{$row['signatursys']}}";
	echo "<br />\n";
}
$rs->Close();


?>
<table class="table">
  <thead>
    <tr>
      <th>Kiste</th>
      <th>#Medien</th>
      <th>letzter Inventarisierungszeitraum</th>
    </tr>
  </thead>
  <tbody>
<?php
	$sql = "SELECT marker, count(*) AS num, MIN(DATE(inventorytime)) AS start, MAX(DATE(inventorytime)) AS end  FROM inventory2 WHERE marker LIKE ".$db->qstr(str_replace("00", "__", $reihe))." GROUP BY marker ORDER BY marker ASC";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		$num = $row['num'];
		echo "<tr>\n";
		echo "  <td><a href=\"inventar.php?func=kiste&kiste={$row['marker']}\">".htmlspecialchars($row['marker'])."</a></td>\n";
		echo "  <td>{$num}</td>\n";
		echo "  <td>{$row['start']}";
		if( $row['start'] != $row['end'] ) echo " - {$row['end']}";
		echo "</td>\n";
		echo "</tr>\n";
	}
	$rs->Close();
?>		
    </tbody>
</table>
