<?php

namespace Mediathek;

include( 'config.inc.php' );
require( 'lib/Mediathek/RIB.class.php' );


$sys = isset( $_REQUEST['sys'] ) ? $_REQUEST['sys'] : null;
$signatures = isset( $_REQUEST['sig'] ) ? ( array )$_REQUEST['sig'] : array();

if( $sys == null || count( $signatures ) < 1 ) return;

if( !isset( $config['RIB'] )) return;

$rib = new RIB( $config['RIB'] );
$rib->load( $sys );
if( !$rib->hasData()) return;

?>
<b>Verfügbarkeit:</b><br />
<dl>
<?php
foreach( $signatures as $sig ) {
    $item = $rib->getAvailability( $sig );
    if( $item ) {
        echo "<dt style=\"font-weight: normal;\">{$sig}</dt>\n";
        echo "<dd style=\"font-weight: normal; margin-left: 20px;\"><span style=\"font-size: 80%; line-height: 80%\">Status: ".( $item['status'] ? ' ausgeliehen bis '.$item['status'] : 'verfügbar' )."<br />\n";
        echo "Benutzung: ".$item['z30-item-status']."</span></dd>\n";
    }  
}
?>
</dl>
