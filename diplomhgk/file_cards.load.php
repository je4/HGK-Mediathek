<p>
</p>
<p>
<?php

include( '../init.inc.php' );
$id = @intval($_GET['id']);

$sql = "SELECT * FROM source_diplom2017_files WHERE idperson=".$id;
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $bg = '';
  if( $row['thumbname'] ) {
    $bg =  " background: url({$row['thumbname']}) center center; background-size: cover;";
  }
?>
<table style="width: 100%;" class="table table-bordered">
  <tr>
    <td style="width: 300px;" rowspan="2">
      <?php   if( $row['thumbname'] && $row['webname'] ) { ?>
      <a href="<?php echo $row['webname']; ?>" target="_blank"><img src="<?php echo $row['thumbname']; ?>" style="max-width: 300px; max-height: 200px;" /></a>
    <?php } else { ?>
      &nbsp;
    <?php } ?>
    </td>
    <td>
      <h4><?php echo $row['name']; ?></h4>
      <p><?php echo $row['mimetype']; ?></p>
      <p>
        <tt>
<?php
    if( preg_match( '/^(audio|video)\//', $row['mimetype'])) {
      $ffprobe = json_decode( $row['tech'], true );
      if( array_key_exists( 'format', $ffprobe )) {
        $format = $ffprobe['format'];
        echo "format<br />";
        if( array_key_exists( 'format_long_name', $format )) echo "&nbsp;&nbsp;&nbsp;container: {$format['format_long_name']}<br />";
        if( array_key_exists( 'duration', $format )) echo "&nbsp;&nbsp;&nbsp;duration:".intval($format['duration'])."sec<br />";
      }
      foreach( $ffprobe['streams'] as $stream ) {
        echo "stream #{$stream['index']}:<br />";
        if( array_key_exists( 'codec_long_name', $stream )) echo "&nbsp;&nbsp;&nbsp;codec: {$stream['codec_long_name']}<br />";
        echo "&nbsp;&nbsp;&nbsp;type:  {$stream['codec_type']}<br />";
        if( array_key_exists( 'width', $stream )) echo "&nbsp;&nbsp;&nbsp;size:  {$stream['width']}x{$stream['height']}<br />";
        if( array_key_exists( 'duration', $stream )) echo "&nbsp;&nbsp;&nbsp;duration:".intval($stream['duration'])."sec<br />";
      }

    }
    elseif( preg_match( '/^(image)\//', $row['mimetype'])) {
      echo "<tt>{$row['tech']}</tt>";
    }
    elseif( preg_match( '/^application\/pdf/', $row['mimetype'])) {
      echo "<tt>{$row['tech']}</tt>";
    }

?>
        </tt>
      </p>
    </td>
  </tr>
</table>
<?php
}
$rs->Close();
 ?>
</p>
