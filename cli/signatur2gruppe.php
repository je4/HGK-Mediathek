<?php

namespace Mediathek;

include '../init.inc.php';

$sql = "DELETE FROM sig_group";
echo $sql."\n";
$rs = $db->Execute( $sql );

$sql = "SELECT DISTINCT a.Signatur FROM ARC a LEFT JOIN sig_group sg ON a.Signatur=sg.signatur WHERE sg.signatur IS NULL AND a.Signatur IS NOT NULL";
echo $sql."\n";
$signatures = array();
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $signatures[] = ( $row['Signatur']);
}
$rs->Close();

$sig_regex = array();
$sql = "SELECT id, regex FROM signaturgroup ORDER BY id DESC";
echo $sql."\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sig_regex[$row['id']] = $row['regex'];
}
$rs->Close();


foreach( $signatures as $sig ) {
		foreach( $sig_regex as $key=>$regex ) {
			if( preg_match($regex, $sig )) {
				if( $key != 1 )
					echo "{$sig} <-- {$regex} : {$key}\n";
				$sql = "INSERT INTO sig_group VALUES( ".$db->qstr( $sig ).", {$key} );";
                echo $sql."\n";
                $db->Execute( $sql );
				break;
			}
		}
}

?>