<?php

include( 'init.inc.php' );
require( 'lib/class.phpmailer.php' );
require( 'lib/class.smtp.php' );

$md5 = null;
do {
  $md5 = md5( time().rand() );
  $sql = "SELECT COUNT(*) FROM form WHERE link=".$db->qstr( $md5 );
  $num = intval( $db->GetOne( $sql ));
} while( $num > 0 );

$sql = "INSERT INTO form( projectid,  status, link, created, changed ) VALUES(
    1
  , ".$db->qstr( 'submit' ). "
  , ".$db->qstr( $md5 ). "
  , NOW()
  , NOW()
  )";

$db->Execute( $sql );
$id = intval( $db->Insert_Id());

$data = $_REQUEST['data'];
foreach( $data  as $key=>$val ){
  $sql = "INSERT INTO formdata( formid, name, value ) VALUES(
      {$id}
    , ".$db->qstr( $key )."
    , ".$db->qstr( $val )."
  )";
  $db->Execute( $sql );
}

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


Der Datenupload für die Einreichung erfolgt unter:
https://mediathek.hgk.fhnw.ch/apply/summe2017/upload.php/{$md5}

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

vielen Dank für die Einreichung von "<?php echo htmlentities( $data['titel'] ); ?>" (<?php echo $data['werkjahr']; ?>) zur Summe.

Es wurde eine Email an <?php echo htmlentities( $data['email'] ); ?> gesendet, die den Link zum Upload enthält.
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
$mail->setFrom('noreply@fhnw.ch', "=?UTF-8?B?".base64_encode("Summe - Formular (no reply)")."?=");
$mail->addAddress('summe@betalabs.ch', utf8_decode( "Sammeladresse Summe" ));
//if( strlen( $card['email2'])) $mail->addAddress($card['email2'], utf8_decode( $card['name']));
$mail->addReplyTo($data['email'], utf8_decode( $data['vorname'].' '.$data['nachname'] ));
$mail->isHTML(false);
$mail->Subject = utf8_decode( "[Summe2017] Anmeldung von {$data['nachname']}, {$data['vorname']}" );

$text = "{$data['vorname']} {$data['nachname']} hat das Formular zur Summe 2017 ausgefüllt.

-------------------
Folgende Daten wurden erfasst:
";
foreach( $data as $key=>$val ) {
  $text.= "{$key}:\n{$val}\n\n";
}
$mail->Body = utf8_decode( $text );

$mail->send();

}
  //print_r( $_REQUEST );
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
