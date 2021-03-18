<?php
namespace Mediathek;

$kuerzel = array( '12733@fhnw.ch' => 'B-CDE',
'12740@fhnw.ch' => 'B-CDE',
'19420@fhnw.ch' => 'B-CDE',
);


include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
  echo 'not logged in';
  exit;
}
if( !$session->inAnyGroup( array( 'mab/upload' ))) {
  echo 'insufficient rights';
  exit;
}

$id = array_key_exists( 'id', $_REQUEST ) ? intval($_REQUEST['id']) : 0;
$systemnummer = array_key_exists( 'systemnummer', $_REQUEST ) ? trim( $_REQUEST['systemnummer'] ) : null;

if( !$id || !$systemnummer ) {
?>
<h3>Fehler: keine Upload-ID oder Systemnummer angegeben</h3>
<?php
  exit;
}

$sql = "SELECT COUNT(*) FROM mab_bs.upload_files WHERE id=".$id;
$num = intval( $db->GetOne( $sql ));
if( !$num ) {
?>
<h3>Fehler: unbekannte Upload-ID</h3>
<?php
  exit;
}

$sql = "INSERT INTO mab_bs.id_systemnummer( id, systemnummer, uniqueid ) VALUES( {$id}, ".$db->qstr( $systemnummer ).", ".$db->qstr( $session->shibGetUniqueID()).")";
$db->Execute( $sql );

$sql = "SELECT * FROM mab_bs.upload_files WHERE id=".$id;
$rs = $db->Execute( $sql );
//echo "<dl>\n";
foreach( $rs as $row ) {
  $sql = "INSERT INTO mediathek_handle.`qr` (`na`, `name`, `type`, `url`, `ishandle`, `text`) VALUES('20.500.12167', ".$db->qstr( 'upload/'.$row['name'].'/'.$row['fileid'] ).", 'data', ".$db->qstr('https://e-medien.mab-bs.ch/data.php?id='.$row['fileid'] ).", 1, ".$db->qstr( $row['name'] ).")";
  $db->Execute( $sql );
  $hdl = "http://hdl.handle.net/20.500.12167/data/upload/{$row['name']}/{$row['fileid']}";
  //echo "<dt>".str_replace('_', ' ', $row['name'])."</dt><dd><a target=\"_blank\" href=\"{$hdl}\">{$hdl}</a></dd>\n";
  $name = str_replace( '_', ' ', pathinfo( $row['name'], PATHINFO_FILENAME ));
  if( preg_match( '/^[^-]+\-(.+)$/', $name, $matches )) $name = trim( $matches[1] );
  echo "<code>\$\$u{$hdl}\$\$zBasel Musik-Akademie: Onlinezugriff {$name}\$\$x".date( 'd.m.Y')."/A332/B-CDE</code><br />\n";
}
//echo "</dl>\n";
$rs->Close();
