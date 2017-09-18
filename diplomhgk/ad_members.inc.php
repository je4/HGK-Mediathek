<?php

$where = "FALSE";
foreach( $param as $p ) $where .= " OR `content` LIKE ".$db->qstr($p)." OR `group` LIKE ".$db->qstr($p)." OR `dn` LIKE ".$db->qstr('%'.str_replace( ',', '%', $p).'%');

$sql = "SELECT DISTINCT content, `group` FROM active_directory.ad_all WHERE ".$where." ORDER BY content ASC";
echo $sql."<br />";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
 ?>
<h2><?php echo htmlspecialchars( $row['content']); ?></h2>
<table>
<?php

  $sql = "SELECT dn FROM active_directory.ad_all WHERE `group`=".$db->qstr( $row['group'] )." ORDER BY dn";
  //echo $sql."<br />";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $dn = $row2['dn'];
?>
<tr><td><?php
    if( preg_match( '/^CN=([^=]+),OU=/i', $dn, $matches )) {
      echo htmlspecialchars(str_replace( "\\", '', $matches[1] ));
    }
    else {
    }
?>   </td>
  <td><?php echo htmlspecialchars( $dn ); ?></td>
<?php
  }
?>
</table>
<?php
}
 ?>
