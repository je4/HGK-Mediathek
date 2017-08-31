<?php

include( '../init.inc.php' );

$formid = intval( $_REQUEST['formid']);
$data = array();
$sql = "SELECT * FROM formdata WHERE formid=".$formid;
$rs = $db->Execute( $sql );
foreach( $rs as $row )
{
  $data[$row['name']] = $row['value'];
}
$rs->Close();
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
     <div class="container-fluid">
       <div class="jumbotron">
         <h1><?php echo htmlspecialchars( $data['titel']); ?> (<?php echo htmlspecialchars( $data['werkjahr']); ?>)</h1>
         <h2><?php echo htmlspecialchars( "{$data['vorname']} {$data['nachname']}"); ?> (*<?php echo htmlspecialchars( $data['jahrgang']); ?>)</h2>
       </div>

       <table style="width: auto !important;" class="table">
         <thead>
           <th colspan="2">Zur Person</th>
         </thead>
         <tbody>
           <tr>
             <td><b>Emailadresse</b></td>
             <td><a href="mailto:<?php echo htmlspecialchars( $data['email']); ?>"><?php echo htmlspecialchars( $data['email']); ?></a></td>
           </tr>
           <tr>
             <td><b>Nachname</b></td>
             <td><?php echo htmlspecialchars( $data['nachname']); ?></td>
           </tr>
           <tr>
             <td><b>Vorname</b></td>
             <td><?php echo htmlspecialchars( $data['vorname']); ?></td>
           </tr>
           <tr>
             <td><b>Jahrgang</b></td>
             <td><?php echo htmlspecialchars( $data['jahrgang']); ?></td>
           </tr>
           <tr>
             <td><b>Adresse</b></td>
             <td><?php echo htmlspecialchars( $data['adresse']); ?></td>
           </tr>
           <tr>
             <td><b>Telefon</b></td>
             <td><?php echo htmlspecialchars( $data['tel']); ?></td>
           </tr>
           <tr>
             <td><b>Webseite</b></td>
             <td><a href="<?php echo htmlspecialchars( (substr( $data['web'], 0, 4 ) == 'http' ? '':'http://').$data['web']); ?>"><?php echo htmlspecialchars( $data['web']); ?></a></td>
           </tr>
         </tbody>
       </table>
<p>&nbsp;</p>
       <table style="width: auto !important;" class="table">
         <thead>
           <th colspan="2">Zum Werk</th>
         </thead>
         <tbody>
           <tr>
             <td><b>Titel</b></td>
             <td><?php echo htmlspecialchars( $data['titel']); ?></td>
           </tr>
           <tr>
             <td><b>Erscheinungsjahr</b></td>
             <td><?php echo htmlspecialchars( $data['werkjahr']); ?></td>
           </tr>
           <tr>
             <td><b>Medium</b></td>
             <td><?php echo htmlspecialchars( $data['medium']); ?></td>
           </tr>
           <tr>
             <td><b>Anderes Format</b></td>
             <td><?php echo htmlspecialchars( $data['anderesformat']); ?></td>
           </tr>
           <tr>
             <td><b>Dauer</b></td>
             <td><?php echo htmlspecialchars( $data['dauer']); ?></td>
           </tr>
           <tr>
             <td><b>Auflage</b></td>
             <td><?php echo htmlspecialchars( $data['auflage']); ?></td>
           </tr>
           <tr>
             <td><b>Ton</b></td>
             <td><?php echo htmlspecialchars( $data['ton']); ?></td>
           </tr>
           <tr>
             <td><b>Sprache</b></td>
             <td><?php echo htmlspecialchars( $data['sprache']); ?></td>
           </tr>
           <tr>
             <td><b>Art/Kategorie</b></td>
             <td><?php echo htmlspecialchars( $data['art']); ?></td>
           </tr>
           <tr>
             <td><b>Kurzbeschreibung</b></td>
             <td><?php echo htmlspecialchars( $data['descr']); ?></td>
           </tr>
           <tr>
             <td><b>Bezug zu Basel</b></td>
             <td><?php echo htmlspecialchars( $data['bezug']); ?></td>
           </tr>
           <tr>
             <td><b>Lizenz</b></td>
             <td><?php echo htmlspecialchars( $data['lizenz']); ?></td>
           </tr>
           <tr>
             <td><b>Nutzung</b></td>
             <td><?php echo htmlspecialchars( $data['rechtesumme']); ?></td>
           </tr>
           <tr>
             <td><b>Mediathek</b></td>
             <td><?php echo htmlspecialchars( $data['rechte']); ?></td>
           </tr>
         </tbody>
       </table>
       <p>&nbsp;</p>
        <table style="width: auto !important;" class="table">
          <thead>
            <th colspan="2">Uploads</th>
          </thead>
          <tbody>
<?php
$sql = "SELECT * FROM files WHERE formid=".$formid;
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $bg = '';
  if( $row['thumbname'] ) {
    $bg =  " background: url({$row['thumbname']}) center center; background-size: cover;";
  }
?>
<table style="width: 100%;" class="table table-bordered">
  <tr>
    <td style="width: 205px;<?php echo $bg; ?>" rowspan="2">
      &nbsp;
    </td>
    <td>
<?php
$link = "https://mediathek.hgk.fhnw.ch".dirname( $_SERVER['SCRIPT_NAME'])."/../{$row['filename']}";

echo "<a href=\"".htmlspecialchars( $link, ENT_QUOTES )."\">{$link}</a><br />\n";

?>
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
      if( array_key_exists( 'streams', $ffprobe )) foreach( $ffprobe['streams'] as $stream ) {
        echo "stream #{$stream['index']}:<br />";
        if( array_key_exists( 'codec_long_name', $stream )) echo "&nbsp;&nbsp;&nbsp;codec: {$stream['codec_long_name']}<br />";
        echo "&nbsp;&nbsp;&nbsp;type:  {$stream['codec_type']}<br />";
        if( array_key_exists( 'width', $stream )) echo "&nbsp;&nbsp;&nbsp;size:  {$stream['width']}x{$stream['height']}<br />";
        if( array_key_exists( 'duration', $stream )) echo "&nbsp;&nbsp;&nbsp;duration:".intval($stream['duration'])."sec<br />";
      }

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
   </div>

 <?php include( '../footer.inc.php' ); ?>

  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
  <script language="javascript">
   $(document).ready( function () {
   });
  </script>
 </body>
 </html>
