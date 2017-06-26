<?php

include( 'init.inc.php' );
$md5 = $_GET['form'];
$sql = "SELECT formid FROM form WHERE link=".$db->qstr( $md5 );
$formid = intval( $db->GetOne( $sql ));

$sql = "SELECT * FROM files WHERE formid=".$formid;
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
?>
<div class="card" style="width: 250px;">
<?php
if( file_exists( $row['filename'].".png")) {
?>
    <img class="card-img-top" style="max-width: 245px; max-height: 245px;" src="<?php echo $row['filename'].".png"; ?>" alt="Card image cap">
<?php
}
 ?>

  <div class="card-block">
    <h4 class="card-title"><?php echo $row['name']; ?></h4>
    <p class="card-text"><?php echo $row['mimetype']; ?></p>
  </div>
</div>
<?php
}
$rs->Close();
 ?>
