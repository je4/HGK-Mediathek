<?php

namespace Mediathek;

include '../init.inc.php';

$videoBase = 'https://ba14ns21403.fhnw.ch/video/intern/summe2017/';

$deckwidth = 500;
$deckheight = 400;

$duration = @intval( $_REQUEST['duration'] );
if( $duration == 0 ) $duration = 3*60;
$cards = @intval( $_REQUEST['cards'] );
if( $cards == 0 ) $cards = 6;
$reload = @intval( $_REQUEST['reload'] );
if( $reload == 0 ) $reload = 6;
//$reload = max( 0, $cards - $reload );

$tmp = json_decode( file_get_contents( 'summe2017.json'), true );
$tmp = $tmp['data'];

$ids = array_keys( $tmp );
$tmp2 = array();
foreach( $ids as $id ) {
  if( isset( $tmp[$id]['info'][0]['video'])) {
    $tmp2[] = $tmp[$id];
  }
}

$data = array();

for( $i = 0; $i < $cards; $i++ ) {
  $id = rand( 0, count( $tmp2 )-1);
  $data[] = $tmp2[$id];
  //unset( $tmp[$id] );
}

//var_dump( $data );

?>

<html>
<head>
  <title>Summe 2017</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <style>
  .card-deck .card {
      /* margin-right: 15px; */
      margin: 10px;
      background-color: #f0f0f0;
      background-image: url("concrete.png");


  }
  .card-deck .card:not(:first-child) {
      margin-left: 10px;
  }
  </style>
</head>
<body style="text-align: center; vertical-align: middle; height: 100%; background-color: black; overflow:hidden;">
  <div class="container-fluid">
    <div class="card-deck-wrapper">
    <div class="card-deck">
<?php
$ids = array();
foreach( $data as $key=>$val ) {
  $ids[] = $key;
  //echo '<!-- '.print_r( $val, true ).' -->';
?>
    <div class="card" style="width: <?php echo $deckwidth; ?>px; text-align: center;">
    <div id="<?php echo $key; ?>" data-video="<?php echo $videoBase.$val['info'][0]['video'] ?>" data-image="<?php echo $videoBase.$val['info'][0]['mediums'][3] ?>" style="padding: 5px; height: <?php echo $deckheight-10; ?>px;">
        <img class="card-img-top" style="width:<?php echo $deckwidth-10; ?>px;" src="<?php echo $videoBase.$val['info'][0]['mediums'][3] ?>" alt="<?php echo htmlspecialchars( $val[3] ); ?>">
    </div>
    <div class="card-block">
      <h4 class="card-title"><?php echo htmlspecialchars( $val[3].' ('.$val[2].')' ); ?></h4>
      <p class="card-text"><?php echo htmlspecialchars( $val[1] ); ?>&nbsp;</p>
    </div>
  </div>
<?php
}
 ?>
  </div>
  </div>
</div>
 <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

<script>
  var current = null;
  var ids = new Array();
  <?php
    foreach( $data as $key=>$val ) echo "ids.push({$key});";
  ?>

  function playOne() {
    if( ids.length == <?php echo $reload; ?> ) {
        window.location.href="summe2017.php?duration=<?php echo $duration; ?>&cards=<?php echo $cards; ?>&reload=<?php echo $reload; ?>";
        return;
    }

    if( current != null ) {
      var src = $('#'+current ).attr('data-image');
      $('#'+current).html('<img class="card-img-top" style="width:<?php echo $deckwidth-10; ?>px;" src="'+src+'">');
    }
    current = ids[Math.floor(Math.random()*ids.length)];
    var src = $('#'+current ).attr('data-video');
    $('#'+current).html('<video autoplay muted style="max-width:710px; max-height: 290px;"><source src="'+src+'" type="video/mp4"></video>');

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
