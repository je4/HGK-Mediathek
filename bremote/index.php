<?php

$datadir = "/data/www/vhosts/mediathek.fhnw.ch/bremote";

$machine = $_REQUEST['machine'];
$os = $_REQUEST['os'];
$arch = $_REQUEST['arch'];

$toml = "{$datadir}/client_{$machine}.toml";
//if( !file_exists($toml)) {
//  die( "{$toml} not found" );
//} 

$bin = "{$datadir}/{$os}/{$arch}/client";
if( $os == 'windows' ) $bin .= '.exe';
if( !file_exists($bin)) {
  die( "{$bin} not found" );
}

chdir( $datadir );
$files = glob("certs/mediathek_*");
$files[] = "client_{$machine}.toml";
$files[] = "{$os}/{$arch}/client" . ($os == 'windows' ? '.exe' : '');

ob_start();
include( 'toml.php' );
$data = ob_get_clean();
file_put_contents( $toml, $data );

$tar = "{$datadir}/{$machine}.tgz";
$cmd = "cd ".escapeshellarg($datadir)."; tar czvf ".escapeshellarg($tar);
foreach( $files as $file ) {
	$cmd .= " " . escapeshellarg($file);
}

//echo $cmd."\n";

exec($cmd);

header('Content-Disposition: attachment; filename="bremote_client.tgz"');

readfile($tar);

unlink($tar);
