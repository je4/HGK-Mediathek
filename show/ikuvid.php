<?php

namespace Mediathek;

include '../init.inc.php';

$duration = @intval( $_REQUEST['duration'] );
if( $duration == 0 ) $duration = 3*60;
$cards = @intval( $_REQUEST['cards'] );
if( $cards == 0 ) $cards = 6;
?>

<html>
<head>
  <title>Videosammlung Institut Kunst</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <style>
  .card-deck .card {
      /* margin-right: 15px; */
      margin: 10px;
  }
  </style>
</head>
<body style="text-align: center; vertical-align: middle; height: 100%;">
  <div class="container-fluid">
    <div class="card-deck-wrapper">
    <div class="card-deck">
<?php
$ids = array();
$sql = "SELECT * FROM source_ikuvid_online ORDER BY RAND() LIMIT 0, ".$cards;
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $ids[] = intval( $row['id'] );
?>
    <div class="card" style="width: 730px; text-align: center;">
    <div id="<?php echo $row['id']; ?>" style="padding: 5px; height: 576px;"><img class="card-img-top" style="width:720px;" src="https://ba14ns21403.fhnw.ch/pics/intern/<?php echo $row['id'] ?>.00003.big.png" alt="<?php echo htmlspecialchars( $row['Titel1'] ); ?>"></div>
    <div class="card-block">
      <h4 class="card-title"><?php echo htmlspecialchars( $row['Titel1'] ); ?></h4>
      <p class="card-text"><?php echo htmlspecialchars( $row['Titel2'] ); ?>&nbsp;</p>
    </div>
  </div>
<?php
}
$rs->Close();
 ?>
  </div>
  </div>
</div>
 <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

<script>
  var current = null;
  var ids = new Array(
  <?php
    foreach( $ids as $id ) echo "{$id}, ";
  ?>);

  function playOne() {
    if( ids.length == 0 ) {
        window.location.href="ikuvid.php?duration=<?php echo $duration; ?>&cards=<?php echo $cards; ?>";
        return;
    }

    if( current != null ) {
      $('#'+current).html('<img class="card-img-top" style="width:720px;" src="https://ba14ns21403.fhnw.ch/pics/intern/'+current+'.00003.big.png">');
    }
    current = ids[Math.floor(Math.random()*ids.length)];
    $('#'+current).html('<video autoplay style="width:720px;"><source src="https://ba14ns21403.fhnw.ch/video/intern/'+current+'.h264-1100k.mp4" type="video/mp4"></video>');

    var index = ids.indexOf(current);
    if (index > -1) {
        ids.splice(index, 1);
    }
    setTimeout( playOne, <?php echo ($duration*1000); ?> );
  }
  $(document).ready( function () {
    playOne();
  });
</script>

</body>
