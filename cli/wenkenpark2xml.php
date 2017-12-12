<?php

/**

http://localhost:8983/solr/base/update?stream.body=%3Cdelete%3E%3Cquery%3Ecatalog%3AWenkenpark%3C%2Fquery%3E%3C%2Fdelete%3E

*/

namespace Mediathek;

$sqlfile = "wenkenpark_handle.sql";


include '../init.inc.php';

function addPersons( $elem, $data, $role=null ) {
  if( !strlen( trim( $data ))) return;
  $ps = explode( ';', $data );
  foreach( $ps as $p ) {
    if( preg_match( '/(.*)\((.*)\)/', $p, $matches )) {
      $p = $matches[1];
      $role = trim( $matches[2] );
    }
    $p = trim( $p, ',; ' );
    $pelem = $elem->addChild( 'Person', $p );
    if( $role ) $pelem->addAttribute( 'Rolle', $role );
  }
}



$data = json_decode( file_get_contents( 'vww.json' ), true );

$xml = new \SimpleXMLElement( '<xml />');

$sql = "SELECT * FROM source_wenkenpark WHERE `Publikationsnummer` IS NOT NULL";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $sys = $row['Publikationsnummer'];

    $meta = $data[$sys];

    $werk = $xml->addChild( 'Werk' );
    $werk->addAttribute( 'id', 'vww-'.$sys );
    $inhalt = $werk->addChild( 'Inhalt' );

    $titel = $inhalt->addChild( 'Titel' );
    $titel->addChild( 'Haupttitel', $row['TITEL'] );

    $beschreibung = $inhalt->addchild( 'Beschreibung' );
    $beschreibung->addChild( 'Beschreibung', $row['KURZBESCHRIEB'] );
    $beschreibung->addChild( 'Genre', $row['Art der Produktion'] );
    $beschreibung->addChild( 'Schlagworte', $row['Schlagworte']);
    if( $row['Sprache'] ) $beschreibung->addChild( 'Sprache', $row['Sprache'] );

    $abdeckung = $inhalt->addChild( 'Abdeckung' );
    $abdeckung->addChild( 'Oertlich', 'Wenkenpark Riehen' );
    $abdeckung->addChild( 'Zeitraum', $row['THEMA'] );

    $kontext = $werk->addChild( 'Kontext' );

    $beteiligte = $kontext->addChild( 'Beteiligte' );
    $autorin = $beteiligte->addChild( 'AutorIn' );
    if( $row['AutorInN1'] ) $autorin->addChild( 'Person', trim( "{$row['AutorInN1']}, {$row['AutorinVN1']}", ' ,' ));
    if( $row['AutorInN2'] ) $autorin->addChild( 'Person', trim( "{$row['AutorInN2']}, {$row['AutorinVN2']}", ' ,' ));
    if( $row['AutorInN3'] ) $autorin->addChild( 'Person', trim( "{$row['AutorInN3']}, {$row['AutorinVN3']}", ' ,' ));

    $mitwirkende = $beteiligte->addChild( 'Mitwirkende' );
    addPersons( $mitwirkende, $row['KAMERA'], 'Kamera' );
    addPersons( $mitwirkende, $row['MUSIK'], 'Musik' );
    addPersons( $mitwirkende, $row['Schnitt'], 'Schnitt' );
    addPersons( $mitwirkende, $row['Ton'], 'Ton' );
    addPersons( $mitwirkende, $row['Mitwirkende'], 'Mitwirkende' );
    if( $row['KonzeptDrehbuchNach1'] ) addPersons( $mitwirkende, "{$row['KonzeptDrehbuchNach1']}, {$row['KonzeptDrehbuchVor1']}", 'Konzept/Drehbuch');
    if( $row['KonzeptDrehbuchNach2'] ) addPersons( $mitwirkende, "{$row['KonzeptDrehbuchNach2']}, {$row['KonzeptDrehbuchVor2']}", 'Konzept/Drehbuch');

    $produzent = $beteiligte->addChild( 'Produzent' );
    addPersons( $produzent, $row['Produktion'], 'Produktion');
    addPersons( $produzent, $row['Koproduktion'], 'Koproduktion');

    $beschreibung = $kontext->addChild( 'Beschreibung' );
    if( $row['Bereich'] ) $beschreibung->addChild( 'Entstehungsumstaende', $row['Bereich'] );
    if( $row['Quelle'] ) $beschreibung->addChild( 'Quelle', $row['Quelle'] );

    $datum = $kontext->addChild( 'Datum' );
    if( $row['Produktionsjahr'] ) $datum->addChild( 'Erstellung', $row['Produktionsjahr'] );

    $tech = $werk->addChild( 'Technische_Informationen' );
    $allgemein = $tech->addChild( 'Allgemein' );
    if( $row['Ursprungsformat'] ) $allgemein->addChild( 'Traegerformat', $row['Ursprungsformat'] );
    if( $row['LAENGE'] ) $allgemein->addChild( 'Dauer', $row['LAENGE'] );
    $bem = array();
    if( $row['Generation'] ) $bem[] = 'Generation: '.$row['Generation'];
    if( $row['Kassetten Typ'] ) $bem[] = 'Kassetten Typ: '.$row['Kassetten Typ'];
    if( $row['Datum 1. Sichtung'] ) $bem[] = 'Datum 1. Sichtung: '.$row['Datum 1. Sichtung'];
    if( $row['Transferdatum'] ) $bem[] = 'Transferdatum: '.$row['Transferdatum'];
    if( $row['Zustand'] ) $bem[] = 'Zustand: '.$row['Zustand'];
    if( count( $bem )) $allgemein->addChild( 'Bemerkung', implode( ";\n", $bem ));

    $video = $tech->addChild( 'Video' );
    if( $row['Farbe sw'] ) $video->addChild( 'Farbe', $row['Farbe sw'] );
    if( $row['TV System'] ) $video->addChild( 'TV_Norm', $row['TV System'] );

    $ton = $tech->addChild( 'Ton' );
    if( $row['Tonart'] ) $ton->addChild( 'Tonaufnahmeverfahren', $row['Tonart'] );

    $systeminfo = $werk->addChild( 'Systeminformationen' );
    $url = $systeminfo->addChild( 'URL' );
    $url->addChild( 'Online_Zugang', 'http://hdl.handle.net/20.500.11806/mediathek/vww-'.$sys );
/*
    if( isset( $meta[1]['video'])) {
      file_put_contents( $sqlfile, "INSERT INTO `handles_handle` (`handle`, `idx`, `type`, `data`, `ttl_type`, `ttl`, `timestamp`, `refs`, `admin_read`, `admin_write`, `pub_read`, `pub_write`) VALUES
('20.500.11806/mediathek/vww-{$sys}/stream', 1, 'URL', ".$db->qstr("{$config['media']['videohybrid']}/vww/{$meta[1]['video']}").", 0, 86400, NULL, NULL, 1, 0, 1, 0);\n", FILE_APPEND);
    }
    elseif( isset( $meta[0]['video'])) {
      file_put_contents( $sqlfile, "INSERT INTO `handles_handle` (`handle`, `idx`, `type`, `data`, `ttl_type`, `ttl`, `timestamp`, `refs`, `admin_read`, `admin_write`, `pub_read`, `pub_write`) VALUES
('20.500.11806/mediathek/vww-{$sys}/stream', 1, 'URL', ".$db->qstr("{$config['media']['videohybrid']}/vww/{$meta[0]['video']}").", 0, 86400, NULL, NULL, 1, 0, 1, 0);\n", FILE_APPEND);
    }
*/
    if( isset( $meta[0]['video'])) {
        $url->addChild( 'Stream', 'http://hdl.handle.net/20.500.11806/mediathek/vww-'.$sys.'/stream' );
    }

    $memobase = $systeminfo->addChild( 'Memobase' );
    switch( $row['Rechte Internet'] ) {
      case 'open':
        $zugang = 'Internet';
        break;
      case 'intern':
        $zugang = 'Memobase; Hochschule für Gestaltung und Kunst Basel / Mediathek';
        break;
      case 'station':
        $zugang = 'Memobase; Sichtungsstation Hochschule für Gestaltung und Kunst Basel / Mediathek';
        break;
      default:
        $zugang = 'kein Zugang';
    }
    $memobase->addChild( 'Zugang', $zugang );
    $memobase->addChild( 'Dokumenttyp', isset( $meta[0]['video']) ? 'Videomaterial' : 'Bilder' );
    $memobase->addChild( 'Unterstuetzt_durch', "Dieses Dokument wurde Dank der Unterstützung von Memoriav erhalten und durch das Center for Digital Matter HGK FHNW online zugänglich gemacht." );
    $id = $systeminfo->addChild( 'ID' );
    $id->addChild( 'Original_ID', 'vww-'.$sys );
    $id->addChild( 'Original_Signatur', $sys );
    $daten = $systeminfo->addChild( 'Daten' );

    $basevideourl = $config['media']['videohybrid'].'/vww/';
    $img = null;
    if( isset( $meta[0]['video'] )) {
      foreach( $meta[0]['mediums'] as $m ) {
        if( strstr( $m, '.still.' ) !== false ) {
          $img = $basevideourl.$m;
          break;
        }
      }
    }
    else {
      $img = $basevideourl.$meta[0]['images'][0];
    }

    //file_put_contents( 'wenkenpark/vww-'.$sys.'.png', file_get_contents( $img ));
    $daten->addChild( 'Vorschaubild', $img );


}
$rs->Close();

echo $xml->asXML();

?>
