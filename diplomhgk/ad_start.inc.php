<table border="0">
<?php
$rights = array_key_exists( $mail, $adauth ) ? $adauth[$mail] : array();

$where = "WHERE false";
foreach( $rights as $r ) $where .= " OR content LIKE ".$db->qstr( $r );
$sql = "SELECT typ, content, `group`, COUNT(*) AS cnt FROM active_directory.ad_all {$where} GROUP BY typ, content, `group` ORDER BY typ, content, `group`";
echo $sql."<br />";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $content = $row['content'];
  $typ = $row['typ'];
  if( $typ == 'acl' ) $content = substr( $content, 25 );
?>
  <tr>
    <td><?php echo htmlspecialchars( $row['typ']); ?></td>
    <td><a href="ad.php?func=members&param[]=<?php echo urlencode( $row['group']); ?>"><?php echo htmlspecialchars( $content ); ?></a></td>
    <td><?php echo htmlspecialchars( $row['group']); ?></td>
    <td><?php echo htmlspecialchars( $row['cnt']); ?></td>
  </tr>
<?php
}
?>
 </table>
