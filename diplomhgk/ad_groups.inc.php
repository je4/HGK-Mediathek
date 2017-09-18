<?php

$where = "FALSE";
foreach( $param as $p ) $where .= " OR `dn` LIKE ".$db->qstr('%'.str_replace( ',', '%', $p).'%');

 ?>
<h2><?php echo htmlspecialchars( implode( '; ', $param )); ?></h2>
<table>
<?php

  $sql = "SELECT * FROM active_directory.distribution WHERE ".$where." ORDER BY dn";
  //echo $sql."<br />";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $dn = $row2['dn'];
    $description = $row2['Description'];
    $mail = $row2['mail'];
?>
<tr><td><?php
    if( preg_match( '/^CN=([^=]+),OU=/i', $dn, $matches )) {
      echo htmlspecialchars(str_replace( "\\", '', $matches[1] ));
    }
    else {
    }
?>   </td>
<td><?php echo htmlspecialchars( $description ); ?></td>
<td><?php echo htmlspecialchars( $mail ); ?></td>
<td><?php echo htmlspecialchars( $dn ); ?></td>
<?php
  }
?>
</table>
<?php
?>
