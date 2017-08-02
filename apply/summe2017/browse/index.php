<?php

include( '../init.inc.php' );

$status = array(
  'submit'=>'Metadaten erfasst',
  'upload'=>'Einreichung vollständig',
)

?><html>
<head>
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
 <style>
 .required {
   color: red;
   text-decoration: none;
 }
 .required:hover {
   color: red;
   text-decoration: none;
 }
 .required:visited {
   color: red;
   text-decoration: none;
 }
 .required:active {
   color: red;
   text-decoration: none;
 }
 </style>
</head>
<body>
 <div class="collapse bg-inverse" id="navbarHeader">
   <?php include( '../header.inc.php' );  ?>
 </div>
     <div class="navbar navbar-inverse bg-inverse">
       <div class="container d-flex justify-content-between">
         <a href="#" class="navbar-brand">Ausschreibung &Sigma; Summe &ndash; VIDEO</a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
         </button>
       </div>
     </div>
     <p />
<?php
$sql = "SELECT status, COUNT(*) AS num FROM form WHERE projectid=1 GROUP BY status";
$stats = array();
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $stats[] = array( 'status'=>$row['status'], 'num'=>$row['num'] );
}
$rs->Close();
//var_dump( $stats );
 ?>
<div class="container-fluid">
  <div class="container">
      <ul class="nav nav-tabs">
<?php foreach( $stats as $key=>$stat ) { ?>
        <li class="nav-item">
          <a class="nav-link<?php echo $key == 0 ? ' active':''; ?>" id="apers" data-toggle="tab" href="#<?php echo $stat['status']; ?>" role="tab"><?php echo "{$status[$stat['status']]} ({$stat['num']})  "; ?></a>
        </li>
<?php } ?>
      </ul>

      <div class="tab-content">
<?php foreach( $stats as $key=>$stat ) { ?>
        <div class="tab-pane fade show active" id="<?php echo $stat['status']; ?>" role="tabpanel">
          <p>
          <?php
            $sql = "SELECT * FROM form WHERE projectid=1 AND status=".$db->qstr( $stat['status'] );
            $rs = $db->Execute( $sql );
            foreach( $rs as $row ) {
              $formid = $row['formid'];
              $status = $row['status'];
              $sql = "SELECT * FROM formdata WHERE formid=".$formid;
              $rs2 = $db->Execute( $sql );
              $meta = array();
              foreach( $rs2 as $row2 ) {
                $meta[$row2['name']] = $row2['value'];
              }
              $rs2->Close();
                echo "<h3>#{$formid} {$status}:<br />\n";
                echo htmlspecialchars( $meta['vorname']).' '.htmlspecialchars( $meta['nachname'])."<br />\n";
                echo htmlspecialchars( $meta['titel']).'('.htmlspecialchars( $meta['werkjahr']).')'."</h3>\n";
          ?>
              <p>
                <table class="table table-striped">
                <thead>
                <tr>
                <th>Name</th>
                <th>Value</th>
                </tr>
                </thead>
                <tbody>
          <?php
            foreach( $meta as $key => $val ) {
          ?>
                   <tr>
                   <td><?php echo htmlspecialchars( $key ); ?></td>
                   <td><?php echo htmlspecialchars( $val ); ?></td>
                   </tr>
          <?php
            }
           ?>
                </tbody>
                </table>
          <?php
            $sql = "SELECT * FROM files WHERE formid=".$formid;
            $rs2 = $db->Execute( $sql );
            foreach( $rs2 as $row2 ) {
              echo "<b>{$row2['name']} ({$row2['mimetype']})</b><br />\n";
              $link = "https://mediathek.hgk.fhnw.ch".dirname( $_SERVER['SCRIPT_NAME'])."/../{$row2['filename']}";
              echo "<a href=\"".htmlspecialchars( $link, ENT_QUOTES )."\">{$link}</a><br />\n";
              if( preg_match( '/^(audio|video)\//', $row2['mimetype'])) {
                $ffprobe = json_decode( $row2['tech'], true );
                if( array_key_exists( 'format', $ffprobe )) {
                  $format = $ffprobe['format'];
                  echo "<pre>format\n";
                  if( array_key_exists( 'format_long_name', $format )) echo "   container: {$format['format_long_name']}\n";
                  if( array_key_exists( 'duration', $format )) echo "   duration:".intval($format['duration'])."sec\n";
                  echo "</pre>\n";
                }
                foreach( $ffprobe['streams'] as $stream ) {
                  echo "<pre>stream #{$stream['index']}:\n";
                  if( array_key_exists( 'codec_long_name', $stream )) echo "   codec: {$stream['codec_long_name']}\n";
                  echo "   type:  {$stream['codec_type']}\n";
                  if( array_key_exists( 'width', $stream )) echo "   size:  {$stream['width']}x{$stream['height']}\n";
                  if( array_key_exists( 'duration', $stream )) echo "   duration:".intval($stream['duration'])."sec\n";
                  echo "</pre>\n";
                }
              }
              echo "<hr /><hr />\n";
            }
            $rs2->Close();
           ?>
              </p>
<?php
  }
  $rs->Close();
 ?>
         </p>
        </div>
<?php } ?>
      </div>
    </div>

</div>

 <?php include( '../footer.inc.php' ); ?>

  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
  <script language="javascript">
   $(document).ready( function () {
  </script>
 </body>
 </html>
