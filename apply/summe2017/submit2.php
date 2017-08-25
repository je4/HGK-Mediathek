<?php

include( 'init.inc.php' );
require( 'lib/class.phpmailer.php' );
require( 'lib/class.smtp.php' );

$md5 = $_GET['form'];
$sql = "SELECT formid, status FROM form WHERE link=".$db->qstr( $md5 );
$row = $db->GetRow( $sql );
$formid = intval( $row['formid']);
$status = $row['status'];

if( $status != 'upload' ) {
  $data = $_REQUEST['data'];

  foreach( $data  as $key=>$val ){
    $sql = "REPLACE INTO formdata( formid, name, value ) VALUES(
        {$formid}
      , ".$db->qstr( $key )."
      , ".$db->qstr( $val )."
    )";
    $db->Execute( $sql );
  }

  $sql = "UPDATE form set status=".$db->qstr( 'upload' )." WHERE formid={$formid}";
  $db->Execute( $sql );
}

$data = array();
$sql = "SELECT * FROM formdata WHERE formid={$formid}";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $data[$row['name']] = $row['value'];
}
$rs->Close();

 ?><html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
</head>
<body>
  <div class="collapse bg-inverse" id="navbarHeader">
<?php include( 'header.inc.php' );  ?>
  </div>
      <div class="navbar navbar-inverse bg-inverse">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand">Ausschreibung &Sigma; Summe &ndash; VIDEO</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>

      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">Ausschreibung für Video-Beiträge zur &Sigma; Summe &ndah; VIDEO 4. – 19. November 2017</h1>
          <p class="lead text-muted">
              Unter dem Zeichen &Sigma; &ndash; für Summe &ndash; versammeln sich unabhängige Projekträume
              aus dem Raum Basel: Wir laden diesesmal zur Videowerkschau ein! Die verschiedenen Räume kuratieren
              eigenständige Ausstellungen und Programme und zeigen im Monat November audiovisuelle
              Arbeiten aus dem aktuellen Videoschaffen.
            </p>
        </div>
      </section>

    <div class="container">
<pre>
<?php
if( $status != 'upload' ) {

$mail = new \PHPMailer();
//    $mail->SMTPDebug = 3;
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'lmailer.ict.fhnw.ch';                    // Specify main and backup SMTP servers
$mail->SMTPAuth = false; // true;                               // Enable SMTP authentication
//    $mail->Username = 'user@example.com';                 // SMTP username
//    $mail->Password = 'secret';                           // SMTP password
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = ''; // 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25; // 587;                                    // TCP port to connect to
//$mail -> charSet = "UTF-8";
$mail->setFrom('noreply@fhnw.ch', "=?UTF-8?B?".base64_encode("Summe - Einreichung (no reply)")."?=");
$mail->addAddress($data['email'], utf8_decode( $data['vorname'].' '.$data['nachname'] ));
//if( strlen( $card['email2'])) $mail->addAddress($card['email2'], utf8_decode( $card['name']));
$mail->addReplyTo('summe@betalabs.ch', 'Die Summe (Basel)');
$mail->isHTML(false);
$mail->Subject = utf8_decode( "Einreichung Summe 2017" );

$text = "Liebe/r {$data['vorname']} {$data['nachname']},

vielen Dank für die Einreichung von \"{$data['titel']}\" ({$data['werkjahr']}) zur Summe 2017.

Die Einreichung ist nun mit den Datenupload abgeschlossen.

Mit lieben Grüssen
 Das Summe-Team
";
/*
$text .= "
-------------------
Sie haben folgende Daten eingereicht:
";
foreach( $data as $key=>$val ) {
  $text.= "{$key}:\n{$val}\n\n";
}
*/
$mail->Body    = utf8_decode( $text );

if(!$mail->send()) {
?>
<div class="alert alert-danger" role="alert">
  Fehler beim Versenden der Email an "<?php echo htmlentities( $data['email'] ); ?>".<br />
  Bitte wenden sie sich an summe@betalabs.ch um ihre Einreichung zu ermöglichen.
</div>
<?php

}
else {
?>
<div class="alert alert-success" role="alert">
  Email an "<?php echo htmlentities( $data['email'] ); ?>" wurde versendet.
</div>
Liebe/r <?php echo "{$data['vorname']} {$data['nachname']}"; ?>,

vielen Dank für die Einreichung von "<?php echo htmlentities( $data['titel'] ); ?>" (<?php echo $data['werkjahr']; ?>) zur Summe 2017.

Die Einreichung ist nun abgeschlossen.

<?php
$mail = new \PHPMailer();
//    $mail->SMTPDebug = 3;
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'lmailer.ict.fhnw.ch';                    // Specify main and backup SMTP servers
$mail->SMTPAuth = false; // true;                               // Enable SMTP authentication
//    $mail->Username = 'user@example.com';                 // SMTP username
//    $mail->Password = 'secret';                           // SMTP password
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = ''; // 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25; // 587;                                    // TCP port to connect to
//$mail -> charSet = "UTF-8";
$mail->setFrom('noreply@fhnw.ch', "=?UTF-8?B?".base64_encode("Summe - Upload (no reply)")."?=");
$mail->addAddress('summe@betalabs.ch', utf8_decode( "Sammeladresse Summe" ));
//if( strlen( $card['email2'])) $mail->addAddress($card['email2'], utf8_decode( $card['name']));
$mail->addReplyTo($data['email'], utf8_decode( $data['vorname'].' '.$data['nachname'] ));
$mail->isHTML(false);
$mail->Subject = utf8_decode( "[Summe2017] Upload von {$data['nachname']}, {$data['vorname']}" );

$text = "{$data['vorname']} {$data['nachname']} hat den Upload zur Summe 2017 abgeschlossen.

-------------------
Folgende Daten wurden erfasst:
";
$sql = "SELECT * FROM files WHERE formid=".$formid;
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $text .= "\n{$row['name']} ({$row['mimetype']})\n";
  $text .= "https://mediathek.hgk.fhnw.ch".dirname( $_SERVER['SCRIPT_NAME'])."/{$row['filename']}\n\n";
  if( preg_match( '/^(audio|video)\//', $row['mimetype'])) {
    $ffprobe = json_decode( $row['tech'], true );
    if( array_key_exists( 'format', $ffprobe )) {
      $format = $ffprobe['format'];
      $text .= "format\n";
      if( array_key_exists( 'format_long_name', $format )) $text .= "   container: {$format['format_long_name']}\n";
      if( array_key_exists( 'duration', $format )) $text .= "   duration:".intval($format['duration'])."sec\n";
    }
    foreach( $ffprobe['streams'] as $stream ) {
      $text .= "stream #{$stream['index']}:\n";
      if( array_key_exists( 'codec_long_name', $stream )) $text .= "   codec: {$stream['codec_long_name']}\n";
      $text .= "   type:  {$stream['codec_type']}\n";
      if( array_key_exists( 'width', $stream )) $text .= "   size:  {$stream['width']}x{$stream['height']}\n";
      if( array_key_exists( 'duration', $stream )) $text .= "   duration:".intval($stream['duration'])."sec\n";
    }
  }
}
$rs->Close();

foreach( $data as $key=>$val ) {
  $text.= "{$key}:\n{$val}\n\n";
}
$mail->Body = utf8_decode( $text );

$mail->send();
}
  //print_r( $_REQUEST );
}
 ?>
 </pre>
    </div>

<hr />
<?php include( 'footer.inc.php' ); ?>

 <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
 <script language="javascript">
  $(document).ready( function () {

  });
 </script>
</body>
</html>
