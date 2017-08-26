<?php

if( !count( $_SERVER )) die( "not for web" );

include( 'init.inc.php' );
require( 'lib/class.phpmailer.php' );
require( 'lib/class.smtp.php' );

$forms = array(
  97	,
99	,
100	,
283	,
174	,
75	,
144	,
10	,
41	,
183	,
184	,
129	,
86	,
92	,
42	,
250	,
72	,
5	,
221	,
223	,
191	,
215	,
185	,
63	,
58	,
59	,
316	,
292	,
237	,
322	,
323	,
201	,
319	,
71	,
);

$sql = "SELECT * from einreichungen WHERE formid IN (".implode( ',', $forms ).")";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  //print_r( $row );
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
  $mail->addAddress($row['email'], utf8_decode( $row['vorname'].' '.$row['nachname'] ));
  //if( strlen( $card['email2'])) $mail->addAddress($card['email2'], utf8_decode( $card['name']));
  $mail->addReplyTo('summe@betalabs.ch', 'Die Summe (Basel)');
  $mail->isHTML(false);
  $mail->Subject = utf8_decode( "Unvollständige Einreichung Summe 2017: ".$row['titel'] );

  $text = "Liebe/r {$row['vorname']} {$row['nachname']},

Sie haben das Werk \"{$row['titel']}\" zur Summe 2017 angemeldet, aber noch keine Daten hochgeladen.
Die Einreichungsfrist ist nun abgelaufen. Wenn Sie Ihr Werk noch zur Summe 2017 submitten möchten,
können Sie ihre Daten bis zum 31.08.2017 unter folgendem Link hochladen:

https://mediathek.hgk.fhnw.ch/apply/summe2017/upload.php/{$row['link']}

Bitte beachten Sie, wenn Sie mehrere Werke angemeldet haben, bekommt jedes Werk einen eigenen Uploadlink.

Mit lieben Grüssen
 Das Summe-Team
";

  $mail->Body    = utf8_decode( $text );
  echo $row['vorname'].' '.$row['nachname']." -> {$row['email']}\n";
  $mail->send();
}
$rs->Close();

?>
