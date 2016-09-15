<?php
namespace Mediathek;

use \Passbook\Pass\Field;
use \Passbook\Pass\Location;
use \Passbook\Pass\Image;
use \Passbook\PassFactory;
use \Passbook\Pass\Barcode;
use \Passbook\Pass\Structure;
use \Passbook\Type\StoreCard;


require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

require( 'lib/class.phpmailer.php' );
require( 'lib/class.smtp.php' );

if( !$session->isLoggedIn()) {
	header( 'Location: auth/?target='.urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
}

echo mediathekheader('search', 'Mediathek - Ausweis', '');
?>
<form action="ausweis.php" method="post" id="generic">
	<input type="hidden" id="action" name="action" />
	<input type="hidden" id="library" name="library" />
	<input type="hidden" id="serial" name="serial" />
</form>	

<div class="back-btn"><i class="ion-ios-search"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-1 col-sm-12">
                <div class="mask">
                </div>
                <div style="left: 20%;" class="main-heading">
                    <h1>Ausweis</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix full-height">
                    <h2 class="small-heading">Mediathek der Künste</h2>
					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
if( $session->isLoggedIn()) {

	$sql = "SELECT *, (expiryDate > DATE_ADD( NOW(), INTERVAL 3 MONTH )) AS ok FROM wallet.card WHERE email LIKE ".$db->qstr( $session->shibGetMail());
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		if( !strlen( $row['uniqueID'] )) {
			$sql = "UPDATE wallet.card SET uniqueID=".$db->qstr( $session->shibGetUniqueID())."
						, name=".$db->qstr( $session->shibGetGivenName().' '.$session->shibGetSurname())." 
					WHERE pass=".$row['pass']." AND serial=".$row['serial'];
			$db->Execute( $sql );
		}
		
	}
	
	$id = @intval( $_REQUEST['library']);
	$barcode = @trim( $_REQUEST['barcode']);
	$email2 = @trim( $_REQUEST['email2']);
	$serial = @intval( $_REQUEST['serial']);
	
	
	
	if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'delete' ) {
		$error = false;
		$sql = "SELECT COUNT(*) FROM wallet.card WHERE uniqueID=".$db->qstr( $session->shibGetUniqueID())." AND pass=".$id." AND serial=".$serial;
		if( !intval( $db->getOne( $sql )) ) {
?>
<div style="background-color: red; color: white; padding: 25px; margin: 15px 0px;">
	<h3>Fehler: Ungültiger Ausweis nicht vorhanden</h3>
</div>
<?php
			$error = true;
		}
		
		if( !$error ) {
			$sql = "UPDATE wallet.card SET deleted=1 WHERE uniqueID=".$db->qstr( $session->shibGetUniqueID())." AND pass=".$id." AND serial=".$serial;
			$db->Execute( $sql );
		}
	}
	elseif( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'send' ) {
		$error = false;
		$sql = "SELECT COUNT(*) FROM wallet.card WHERE uniqueID=".$db->qstr( $session->shibGetUniqueID())." AND pass=".$id." AND serial=".$serial;
		if( !intval( $db->getOne( $sql )) ) {
?>
<div style="background-color: red; color: white; padding: 25px; margin: 15px 0px;">
	<h3>Fehler: Ungültiger Ausweis nicht vorhanden</h3>
</div>
<?php
			$error = true;
		}
		
		if( !$error ) {
			$sql = "SELECT * FROM wallet.pass WHERE id=".$id;
			$pass = $db->getRow( $sql );
			$passFile = Helper::createPass( $id, $serial );
			if( $passFile !== false ) {
				$sql = "SELECT * FROM wallet.card WHERE pass=".$id." AND serial=".$serial;
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
https://play.google.com/store/search?q=apple%20wallet&c=apps&hl=en

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
	<h4>Der Ausweis "<?php echo htmlspecialchars($pass['logotext']); ?>" wurde an ihre Emailadresse(n) gesendet.</h4>
</div><?php					
				}
			}
		}
		
	}
	elseif( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'add' ) {
		$error = false;
		$sql = "SELECT COUNT(*) FROM wallet.pass WHERE id=".$id;
		if( !intval( $db->getOne( $sql )) ) {
?>
<div class="alert alert-danger" role="alert">
	<h4>Fehler: Ungültiger Ausweistyp</h4>
</div>
<?php
			$error = true;
		}
		if( strlen( $email2 ) && filter_var($email2, FILTER_VALIDATE_EMAIL) === false ) {
?>
<div class="alert alert-danger" role="alert">
	<h4>Fehler: ungültiges Emailformat</h4>
</div>
<?php
			$error = true;
		}
		if( !$error ) {
			$sql = "SELECT count(*) FROM wallet.card WHERE uniqueID=".$db->qstr( $session->shibGetUniqueID())." AND pass=".$id." AND barcode=".$db->qstr( $barcode )." AND uniqueID=".$db->qstr( $session->shibGetUniqueID());
			if( intval( $db->getOne( $sql ))) {
				$sql = "UPDATE wallet.card SET name=".$db->qstr( $session->shibGetGivenName().' '.$session->shibGetSurname())."
						, barcode=".$db->qstr( $barcode )."
						, email2=".$db->qstr( $email2 )."
						, deleted=0
					WHERE uniqueID=".$db->qstr( $session->shibGetUniqueID())." AND pass=".$id." AND barcode=".$db->qstr( $barcode )." AND uniqueID=".$db->qstr( $session->shibGetUniqueID());			
			}
			else {
				$sql = "INSERT INTO wallet.card( pass, barcode, uniqueID, email, email2, name )
				   VALUES( ".$id	.", ".$db->qstr( $barcode )
						.", ".$db->qstr( $session->shibGetUniqueID())
						.", ".$db->qstr( $session->shibGetMail())
						.", ".$db->qstr( $email2 )
						.", ".$db->qstr( $session->shibGetGivenName().' '.$session->shibGetSurname())
						.")";
			}
			$db->Execute( $sql );
		}
	}
	
?>
<div style="background-color: #f4f4f4; padding: 25px; margin: 15px 0px;">
<h3>Digitalen Bibliotheksausweis erstellen</h3>
<p>&nbsp;</p>
<h4><?php echo htmlspecialchars( $session->shibGetGivenName().' '.$session->shibGetSurname()); ?></h4>
<h5><?php echo htmlspecialchars( $session->shibGetMail()); ?></h5>
<p>&nbsp;</p>
<form action="ausweis.php" method="post">
	<input type="hidden" name="action" value="add" />
  <div class="form-group">
		<label for="library" style="text-align: left;">Stammbibliothek</label>
		<select class="form-control" id="library" name="library">
<?php
	$sql = "SELECT id,displayname FROM wallet.pass WHERE active=1 ORDER BY sort, displayname ASC";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		echo '    <option value="'.$row['id'].'"'.($row['id'] == $id ? ' selected':'').'>'.htmlspecialchars( $row['displayname'] ).'</option>'."\n";
	}
	$rs->Close();	
?>
		</select>
  </div>
	<div class="form-inline">
	  <div class="form-group">
		<label for="ausweisnummer">Ausweisnummer</label>
		<input class="form-control" type="text" id="ausweisnummer" name="barcode" value="<?php echo htmlentities( $barcode ); ?>">
	  </div>
	  &nbsp;&nbsp;
	  <div class="form-group">
		<label for="email2">Emailadresse 2 (optional)</label>
		<input class="form-control" type="text" id="email2" name="email2" value="<?php echo htmlentities( $email2 ); ?>">
	  </div>
	  &nbsp;&nbsp;
	  <button type="submit" class="btn btn-primary">Beantragen</button>
	</div>
</form>
</div>

<p>&nbsp;</p>
<div style="background-color: white; padding: 25px; margin: 15px 0px;">
	<h3>Ausweise</h3>

<div class="row">
<?php
$sql = "SELECT p.id as library, c.serial, p.description, c.barcode, c.email, c.email2, DATE(c.expirydate) as expirydate, c.valid FROM wallet.card c, wallet.pass p
			WHERE deleted=0 AND c.pass=p.id AND c.uniqueID=".$db->qstr( $session->shibGetUniqueID());
$rs = $db->Execute( $sql );
$num = $rs->RecordCount();
if( $num ) {
	$w = 3;
	if( $num <= 4 ) $w = 12/$num;
	foreach( $rs as $row ) {
?>
	<div class="col-lg-<?php echo $w; ?>">
		<div class="card card-block">
			<div class="card-header">
			<?php echo htmlspecialchars( $row['serial'] ); ?>
			</div>
			<div class="card-block">
				<h4 class="card-title"><?php echo htmlspecialchars( $row['description'] ); ?></h4>
				<p class="card-text"><?php echo htmlspecialchars( $row['barcode'] ); ?><br />
				    <?php echo htmlspecialchars( $row['email'] ); ?><br />
				    <?php echo htmlspecialchars( $row['email2'] ); ?><br />
				    <?php echo htmlspecialchars( $row['expirydate'] ); ?><br />
					<?php echo $row['valid'] ? '' : 'nicht '; ?>geprüft
				</p>
			</div>
			<div class="card-footer">
			<button onclick="cardsend(<?php echo $row['library']; ?>, '<?php echo trim( $row['serial']); ?>');" class="btn btn-primary" style="padding: 7px 14px; margin: 3px;">Zusenden</button>
			<button onclick="carddelete(<?php echo $row['library']; ?>, '<?php echo trim( $row['serial']); ?>');" class="btn btn-primary" style="padding: 7px 14px; margin: 3px;">Löschen</button>
			</div>
		</div>
	</div>
<?php
	}
	$rs->Close();
}
?>
</div>
</div>
<pre>
<!-- <?php nl2br (htmlspecialchars( print_r( $_SERVER ))); ?> -->
</pre>

<?php  } else { ?>
Bitte <a href="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>">anmelden</a>, um einen digitalen NEBIS-Ausweis zu erstellen oder die persönlichen Einstellungen anzupassen.
<?php } ?>
					
					</div>
<?php
	$grps = array();
	foreach( $session->getGroups() as $grp ) $grps[] = $db->qstr( $grp );
	$sql = 'SELECT * FROM wallet.pass WHERE `group` IN ('.implode( ', ', $grps ).")";
	echo "<!-- {$sql} -->\n";
	$passes = $db->GetAll( $sql );
	$num = count( $passes );
	if( $num ) {
		$passids = array();
		foreach( $passes as $pass ) $passids[] = $pass['id'];
		
?>
<div style="background-color: white; padding: 25px; margin: 15px 0px;">
	<h3>Ausweise bearbeiten</h3>
<form id="idedit">
	<table class="table table-bordered" id="idtable">
		<thead>
			<tr>
				<th>Seriennummer</th>
				<th>Ausweis</th>
				<th>Name</th>
				<th>Email</th>
				<th>Barcode</th>
				<th>Geprüft</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Seriennummer</th>
				<th>Ausweis</th>
				<th>Name</th>
				<th>Email</th>
				<th>Barcode</th>
				<th>Geprüft</th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
		<tbody>
			<tr class="idsearch">
				<td><input type="text" id="serial" name="serial" value="" onkeyup="searchid();"></td>
				<td><select class="form-control" id="library" name="library" onchange="searchid();">
					<option value="0">Alle</option>
<?php
foreach( $passes as $pass )
	echo "				<option value=\"{$pass['id']}\">".htmlentities( $pass['displayname'])."</option>\n";
?>
				</select></td>
				<td><input type="text" id="name" name="name" value="" onkeyup="searchid();"></td>
				<td><input type="text" id="email" name="email" value="" onkeyup="searchid();"></td>
				<td><input type="text" id="barcode" name="barcode" value="" onkeyup="searchid();"></td>
				<td><select class="form-control" id="valid" name="valid" onchange="searchid();">
					<option value="-1">egal</option>
					<option value="1">ja</option>
					<option value="0" selected>nein</option>
				</select></td>
				<td><!-- <button onclick="searchid();" class="btn btn-primary" style="padding: 7px 14px;">Suchen</button> --></td>
			</tr>
		</tbody>
	</table>
</form>	
</div>
<?php
	}
?>
					
				</div>
			</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Copyright &copy; 2016
							<a href="#">Mediathek der Künste</a>
						</p>
					</div>

					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="author">
							<a href="http://www.fhnw.ch/hgk" target="_blank">HGK FHNW</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
	
<?php
//include( 'bgimage.inc.php' );
?>
<script>


function reloadAusweis( action, _library, _serial ) {
	form = $("#idedit");
	serial = form.find("#serial").val();
	passid = form.find("#library option:selected").val();
	name = form.find("#name").val();
	email = form.find("#email").val();
	barcode = form.find("#barcode").val();
	valid = form.find("#valid option:selected").val();
	
	console.log( serial+"/"+passid+"/"+name+"/"+email+"/"+barcode+"/"+valid );
	
	
	$.get( 'ausweis.load.php?serial='+encodeURIComponent(serial)
		  +'&serial='+encodeURIComponent(serial)
		  +'&passid='+encodeURIComponent(passid)
		  +'&name='+encodeURIComponent(name)
		  +'&email='+encodeURIComponent(email)
		  +'&barcode='+encodeURIComponent(barcode)
		  +'&valid='+encodeURIComponent(valid)
		  +'&action='+encodeURIComponent(action)
		  +'&_library='+encodeURIComponent(_library)
		  +'&_serial='+encodeURIComponent(_serial)
		  , function( data ) {
				$("#idtable").find( "tr.iddata" ).replaceWith( '' );
				$("#idtable").find( "tr.idsearch" ).after( data );
				$.fn.editable.defaults.mode = 'inline';
				$('.x-editable').editable( { showbuttons: false });
	});
}

function searchid() {
	reloadAusweis( 'search', null, null );
}	

function validateSendAusweis( library, serial ) {
	reloadAusweis( 'validate_send', library, serial );
}

function sendAusweis( library, serial ) {
	reloadAusweis( 'send', library, serial );
}

function cardsend(library, serial ) {
    $('#generic').children( '#action' ).val( 'send' );
    $('#generic').children( '#library' ).val( library );
    $('#generic').children( '#serial' ).val( serial );
    $('#generic').submit();
}

function carddelete(library, serial ) {
	if ( confirm( "Ausweis Nr. " + serial + " löschen?" )) {
		$('#generic').children( '#action' ).val( 'delete' );
		$('#generic').children( '#library' ).val( library );
		$('#generic').children( '#serial' ).val( serial );
		$('#generic').submit();
    }
}

function init() {
	
	$(".dropdown-menu a").click(function(){
		var selText = $(this).text();
		$(this).parents('.dropdown').find('.dropdown-toggle').html(selText);
	  });
	
	$('.carousel-shot').carousel({
	  interval: 2000
	});

	$('body').on('click', '.back-btn', function () {
		window.location="search.php";
	});
	
	searchid();
}
</script>   

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>

<?php
echo mediathekfooter();
?>

  
