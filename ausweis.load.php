<?php
namespace Mediathek;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include( 'init.inc.php' );
include( 'lib/class.phpmailer.php' );
include( 'lib/class.smtp.php' );

$serial = @trim( $_REQUEST['serial']);
$passid = @intval( $_REQUEST['passid']);
$name = @trim( $_REQUEST['name']);
$email = @trim( $_REQUEST['email']);
$barcode = @trim( $_REQUEST['barcode']);
$valid = @intval( $_REQUEST['valid']);
$action = @trim( $_REQUEST['action']);
$_library = @intval( $_REQUEST['_library']);
$_serial = @intval( $_REQUEST['_serial']);

$grps = array();
foreach( $session->getGroups() as $grp ) $grps[] = $db->qstr( $grp );

switch( $action ) {
	case 'validate_send':
		$sql = "SELECT COUNT(*) FROM wallet.card c, wallet.pass p WHERE c.pass=p.id AND deleted=0 AND p.group IN (".implode( ', ', $grps ).") AND c.serial={$_serial} and c.pass={$_library}";
		if( !intval( $db->GetOne( $sql ))) {
			echo "<!-- {$sql} -->\n";
			echo "<!-- Fehler: ungenügende Rechte -->\n";
		}
		$sql = "UPDATE wallet.card SET valid=1 WHERE pass={$_library} AND serial={$_serial}";
		$db->Execute( $sql );
	case 'send':
		$passFile = Helper::createPass( $_library, $_serial );
		$sql = "SELECT * FROM wallet.pass WHERE id=".$_library;
		$pass = $db->getRow( $sql );
		$sql = "SELECT * FROM wallet.card WHERE pass=".$_library." AND serial=".$_serial;
		$card = $db->getRow( $sql );
				$text = "Ihr digitaler Bibliotheksausweis ist fertig!
Your electronic library card is ready!
Stammbibliothek/Home Library: {$pass['logotext']}

Antragsteller/User: {$card['name']}
Seriennummer/Serial Number: {$card['serial']}
Barcode: {$card['barcode']}
Ablaufdatum/Expires: {$card['expirydate']}


Android User: Es wird eine App benötigt, welche in der Lage ist Apple-Wallets anzuzeigen:
https://play.google.com/store/search?q=apple%20wallet&c=apps&hl=de

Android User: You need an app to display Apple Wallets:
https://play.google.com/store/search?q=apple%20wallet&c=apps&hl=de

Mit freundlichen Grüssen / kind regards,
{$pass['logotext']}
";

	
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
		$mail->setFrom('noreply@fhnw.ch', "=?UTF-8?B?".base64_encode("{$pass['logotext']} (no reply)")."?=");
		$mail->addAddress($card['email'], $card['name']);
		if( strlen( $card['email2'])) $mail->addAddress($card['email2'], utf8_decode( $card['name']));
		$mail->isHTML(false);
		$mail->Subject = utf8_decode( "Digitaler Ausweis: ".$pass['logotext'] );
		$mail->Body    = utf8_decode( $text );
		$mail->addAttachment( $passFile );
		
?>
	<tr class="iddata"><td colspan="8">
<?php		
		if(!$mail->send()) {
?>
<div class="alert alert-danger" role="alert">
	<h4>Email konnte nicht gesendet werden</h4>
	Fehler: <?php echo htmlspecialchars( $mail->ErrorInfo ); ?>
</div>
<?php					
		} else {
?>
<div class="alert alert-success" role="alert">
	<h4>Der Ausweis "<?php echo htmlspecialchars($pass['logotext']."[{$_serial}]"); ?>" wurde versendet.</h4>
</div><?php					
		}
?>
	</td></tr>
<?php		
	break;
}

$grps = array();
foreach( $session->getGroups() as $grp ) $grps[] = $db->qstr( $grp );
$sql = "SELECT c.*, p.displayname FROM wallet.card c, wallet.pass p WHERE c.pass=p.id AND deleted=0 AND p.group IN (".implode( ', ', $grps ).")";
if( strlen( $serial )) $sql .= " AND CONVERT( serial, char ) LIKE ".$db->qstr( $serial.'%' );
if( $passid ) $sql .= " AND p.id=".$passid;
if( strlen( $name )) $sql .= " AND c.name LIKE ".$db->qstr( $name.'%' );
if( strlen( $email )) $sql .= " AND (c.email LIKE ".$db->qstr( $email.'%' )." OR c.email2 LIKE ".$db->qstr( $email.'%' ).")";
if( strlen( $barcode )) $sql .= " AND c.barcode LIKE ".$db->qstr( $barcode.'%' );
if( $valid != -1 ) $sql .= " AND c.valid=".$valid;
$sql .= " ORDER BY serial DESC";
echo "<!-- {$sql} -->\n";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
?>
			<tr class="iddata">
				<td><?php echo htmlentities( $row['serial'] ); ?></td>
				<td><?php echo htmlentities( $row['displayname'] ); ?></td>
				<td><?php echo '<a href="#" data-pk="'.$row['serial'].'-'.$row['pass'].'" data-type="text" data-url="ausweis.update.php" data-name="name" id="name_'.$row['serial'].'" class="x-editable" )">'.htmlentities( $row['name'] ).'</a>'; ?></td>
				<td><?php echo htmlentities( $row['email'] ); ?><br />
					<?php echo '<a href="#" data-pk="'.$row['serial'].'-'.$row['pass'].'" data-type="text" data-url="ausweis.update.php" data-name="email2" id="email2_'.$row['serial'].'" class="x-editable" )">'.htmlentities( $row['email2'] ).'</a>'; ?></td>
				<td><?php echo '<a href="#" data-pk="'.$row['serial'].'-'.$row['pass'].'" data-type="text" data-url="ausweis.update.php" data-name="barcode" id="barcode_'.$row['serial'].'" class="x-editable" )">'.htmlentities( $row['barcode'] ).'</a>'; ?></td>
				<td><?php echo htmlentities( $row['valid'] ? 'ja':'nein' ); ?></td>
				<?php if( $row['valid'] ) { ?>
				<td><button onclick="sendAusweis(<?php echo $row['pass']; ?>, '<?php echo trim( $row['serial']); ?>'); return false;" class="btn btn-primary" style="padding: 7px 14px; margin: 3px;">Versenden</button></td>
				<?php } else { ?>
				<td><button onclick="validateSendAusweis(<?php echo $row['pass']; ?>, '<?php echo trim( $row['serial']); ?>'); return false;" class="btn btn-primary" style="padding: 7px 14px; margin: 3px;">Validieren und Versenden</button></td>
				<?php } ?>
			</tr>
<?php
}
$rs->Close();
?>
