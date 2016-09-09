<?php

$sql = 'SELECT COUNT(*) AS num, MIN(DATE(inventorytime)) AS start, MAX(DATE(inventorytime)) AS end, CONCAT( SUBSTRING(marker, 1, 3), "00_", SUBSTRING(marker, 7, 1)) AS reihe
    FROM inventory_cache
    WHERE marker REGEXP "^[A-Z]_\\\\d{3}_[ab]"
    GROUP BY reihe ORDER BY reihe ASC';
    
$rs = $db->Execute( $sql );
//echo "<pre>".$sql."</pre>";
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Reihe</th>
            <th>#Medien</th>
            <th>Start</th>
            <th>Ende</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach( $rs as $row ) {
?>
        <tr>
            <td><a href="inventar.php?func=reihe&reihe=<?php echo urlencode( $row['reihe'] ); ?>"><?php echo htmlspecialchars( $row['reihe'] ); ?></a></td>
            <td><?php echo htmlspecialchars( $row['num'] ); ?></td>
            <td><?php echo htmlspecialchars( $row['start'] ); ?></td>
            <td><?php echo htmlspecialchars( $row['end'] ); ?></td>
        </tr>
<?php
}
$rs->Close();
?>
    </tbody>
</table>