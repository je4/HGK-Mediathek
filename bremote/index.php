<?php

$datadir = "/data/www/vhosts/mediathek.fhnw.ch/bremote";
$devmachine = 'ba14nc2109x';

$machine = $_REQUEST['machine'];
$machine = str_replace('.local', '', strtolower($machine));
$os = $_REQUEST['os'];
$arch = $_REQUEST['arch'];
 

if( $machine == $devmachine ) {
	$datadir .= '/new';
}

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
//$files = glob("certs/mediathek_*");

$files = [
	'certs/info_age_ca.pem',
	'certs/info_age_localhost.key',
	'certs/info_age_localhost.pem',
	"certs/info_age_mediathek_{$machine}.key",
	"certs/info_age_mediathek_{$machine}.pem",
	"startup.sh",
];
$files[] = "client_{$machine}.toml";
$files[] = "{$os}/{$arch}/client" . ($os == 'windows' ? '.exe' : '');
$files[] = "{$os}/{$arch}/client-old" . ($os == 'windows' ? '.exe' : '');

ob_start();
if( $machine == $devmachine ) {
	include( 'toml_new.php' );
} else {
	include( 'toml.php' );
}
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
