<?php

include( '../init.inc.php' );

$exportdir = '/data2/apply/summe2017/export/';
$basedir = '/data2/apply/summe2017/';

$sql = "SELECT * FROM einreichungen WHERE status='upload' ORDER BY nachname, vorname, titel";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  $formid = $row['formid'];
  $folder = "{$formid} - {$row['vorname']} {$row['nachname']}";
  $folder = preg_replace(
    array('/[^a-zA-Z0-9 ._-]/'),
    array('_'), iconv('UTF-8', 'ASCII//TRANSLIT', $folder));
  if( !is_dir( $exportdir.$folder )) {
    echo " mkdir ".$exportdir.$folder."\n";
    mkdir( $exportdir.$folder );
  }
  $sql = "SELECT * FROM files WHERE formid=".$formid;
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $target = preg_replace(
      array('/[^a-zA-Z0-9 ._-]/'),
      array('_'), iconv('UTF-8', 'ASCII//TRANSLIT', $row2['name']));
    $t = $exportdir.$folder.'/'.$target;
    $s = $basedir.$row2['filename'];
    $cmd = "ln ".escapeshellarg($s)." ".escapeshellarg($t);
    if( !file_exists( $t )) {
      echo $cmd."\n";
      `$cmd`;
    }
//    link( $t, $s);
  }
  $rs2->Close();

  $data = array();
  $sql = "SELECT * FROM formdata WHERE formid=".$formid;
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 )
  {
    $data[$row2['name']] = $row2['value'];
  }
  $rs2->Close();
  print_r( $data );
  //continue;
  ob_start();
?>
<html>
<head>
  <title><?php echo htmlspecialchars( $data['titel']); ?></title>
</head>
<body>
  <h1><?php echo htmlspecialchars( $data['titel']); ?> (<?php echo htmlspecialchars( $data['werkjahr']); ?>)</h1>
  <h2><?php echo htmlspecialchars( "{$data['vorname']} {$data['nachname']}"); ?> (*<?php echo htmlspecialchars( $data['jahrgang']); ?>)</h2>

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
</body>
</html>
<?php
  $str = ob_get_clean();

  file_put_contents( $exportdir.$folder.'/metadata.html', $str );
}
$rs->Close();
?>
