<?php
  global $config;

  include( '../init.inc.php' );
  $jwtkey = 'pP7ZMIAWY2zHgP3Nrge9';
  $bgcolor = "#203030";
  if( isset( $_REQUEST['bgcolor'])) $bgcolor = $_REQUEST['bgcolor'];

if( !$session->isLoggedIn()) {
  header( 'Location: https://mediathek.hgk.fhnw.ch/auth?target='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
  exit;
}

if( !$session->inGroup( 'hgk/vonarx' ) && !$session->isAdmin()) {
  die( 'insufficient rights' );
}

$sql = "SELECT * FROM source_vonarx_box1 WHERE ObjectNr LIKE 'pva.1.1.%' ORDER BY RAND() LIMIT 0,1";
$row = $db->GetRow( $sql );

$subject = 'PeterVonArx/'.$row['ObjectNr'].'/iiif/info.json';
$payload = [
          'iss' => 'DIGMA Mediaserver',
          'sub' => strtolower( $config['mediaserver']['sub_prefix'].$subject),
          'exp' => time() + 4 * 3600,
        ];
$token = jwt_encode($payload, $jwtkey);

?><!DOCTYPE html>
  <html>
  <head>
  <meta charset="UTF-8">
  <title>DIGMA Mediaserver 2 / IIIF Viewer</title>
  </head>
  <body style="margin: 0px 0px 0px 0px; background-color: <?php echo $bgcolor; ?>;">

    <div id="openseadragon1"
  		style="width: 100vw; height: 100vh"></div>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/openseadragon/2.4.0/openseadragon.js"></script>
  	<script>
    var animationTime = 5;
    var viewport = null;
    var maxZoom = null;
    var minZoom = null;
    var vpBounds = null;
    var cntBounds = null;

  	var viewer = OpenSeadragon({
  	    id: "openseadragon1",
  	    prefixUrl: "https://cdnjs.cloudflare.com/ajax/libs/openseadragon/2.4.0/images/",
  	    immediateRender: true,
  	    showNavigator: true,
        showNavigationControl: true,
        navigatorAutoFade: false,
        autoHideControls: true,
        showZoomControl: false,
        showHomeControl: false,
        showFullPageControl: false,
        showRotationControl: false,
        showFlipControl: false,
        animationTime: animationTime,
        springStiffness: 0.5,
        debugMode:  true,
  		  tileSources: "<?php echo $config['mediaserver']['baseurl'].$subject.'?token='.$token;?>"
  	});
    viewer.addHandler('open', function (event) {
      console.log('open');
      viewer.setControlsEnabled(false);
      viewport = viewer.viewport;
      maxZoom = viewport.getMaxZoom();
      minZoom = viewport.getMinZoom();
      vpBounds = viewport.getBounds();
      cntBounds = viewport._contentBounds;
      //var itemBounds = viewport.world.getItemAt(0).getBounds();
      console.log( 'max-zoom: '+maxZoom );
      console.log( 'min-zoom: '+minZoom );
      viewport.zoomTo( maxZoom-(maxZoom-minZoom)*2/3, new OpenSeadragon.Point(vpBounds.width/2, vpBounds.height/2));
      setTimeout(function() {
        vpBounds = viewport.getBounds();
        console.log( "oben links" );
        // oben links
        viewport.panTo(new OpenSeadragon.Point(vpBounds.width/2, vpBounds.height/2))
        setTimeout(function() {
          console.log( "unten links" );
          // unten links
          viewport.panTo(new OpenSeadragon.Point(vpBounds.width/2, cntBounds.height-vpBounds.height/2))
          setTimeout(function() {
            console.log( "mitte rechts" );
            // mitte rechts
            viewport.panTo(new OpenSeadragon.Point(cntBounds.width-vpBounds.width/2, 0))
            setTimeout(function() {
              console.log( "back to home" );
              // mitte rechts
              viewport.goHome();
              setTimeout(function() {
                console.log( "reload" );
                // mitte rechts
                //window.location.reload();
              }, animationTime*1100);
            }, animationTime*1100);
          }, animationTime*1100);
        }, animationTime*1100);
      }, animationTime*1100);

    });

  </script>
  </body>
  </html>
