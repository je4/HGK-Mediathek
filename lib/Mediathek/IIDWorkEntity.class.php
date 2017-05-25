<?php
/**
 * This file is part of MediathekMiner.
 * MediathekMiner is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see http://www.gnu.org/licenses/.
 *
 *
 * @package     Mediathek
 * @subpackage  NebisImport
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 *
 */

/**
 * @namespace
 */

namespace Mediathek;

/**
 * Handling of MARC Data from Nebis
 *
 */

class IIDWorkEntity extends SOLRSource {

    private $idprefix = 'iidwork';
    private $idmoduleprefix = 'iidmod';
    private $db = null;



    function __construct( \ADOConnection $db ) {
        $this->db = $db;
    }

    public function loadFromDoc( $doc) {
    	$this->data = json_decode( gzdecode( base64_decode( $doc->metagz )), true);
    	$this->id = $doc->originalid;
    }

    public function reset() {
      $this->data = null;
      $this->id = null;
    }

    function loadFromDatabase( string $id ) {
        global $config;

        $this->reset();

        $this->id = $id;
        //$this->idprefix = $idprefix;

        $sql = "SELECT * FROM archiv_iid.Arbeiten WHERE `idArbeiten` = ".intval( $id );
        $row = $this->db->GetRow( $sql );
        $this->data = array();
        $this->data['Titel'] = $row['Titel'];
        $this->data['Kurzbeschreibung'] = $row['Kurzbeschreibung'];
        $this->data['Abstract'] = $row['Abstract'];
        $this->data['TextZurArbeit'] = $row['TextZurArbeit'];
        $this->data['Zusammenarbeit'] = $row['Zusammenarbeit'];
        $this->data['Recherche'] = $row['Recherche'];
        $this->data['Auszeichnungen'] = $row['Auszeichnungen'];
        $this->data['Bemerkungen'] = $row['Bemerkungen'];
        $this->data['Kontakt'] = $row['Kontakt'];
        $this->data['Pfad'] = null; // $row['Pfad'];
        $this->data['Bild1'] = $row['Bild1'];
        if( !strlen( $this->data['Pfad']) && strlen( $this->data['Bild1'])) {
          $dir = new \RecursiveDirectoryIterator($config['iid']['mediapath'].'/upload');
          $ite = new \RecursiveIteratorIterator($dir);
          $files = new \RegexIterator($ite, "/{$this->data['Bild1']}.jpg/", \RegexIterator::MATCH);
          foreach($files as $file) {
//            var_dump( $file );
            $this->data['Pfad'] = substr( $file->getPath(), strlen($config['iid']['mediapath'].'/upload'));
            echo $this->data['Pfad']."\n";
//            exit;
            break;
          }
        }
        $this->data['Bild2'] = $row['Bild2'];
        $this->data['Bild3'] = $row['Bild3'];
        $this->data['Website'] = $row['Website'];
        $this->data['ExterneDoku'] = $row['ExterneDoku'];
        $this->data['Modul'] = array();
        $sql = "SELECT *, YEAR( Uebergabe ) AS Jahr FROM archiv_iid.Module WHERE idModule=".$row['Module_idModule'];
        $row2 = $this->db->getRow( $sql );
        if( $row2 ) {
          $this->data['Modul']['id'] = $row2['idModule'];
          $this->data['Modul']['Titel'] = $row2['Titel'];
          $this->data['Modul']['Kennziffer'] = $row2['Kennziffer'];
          $this->data['Modul']['Uebergabe'] = $row2['Uebergabe'];
          $this->data['Modul']['Jahr'] = $row2['Jahr'];
        }
        $this->data['Dokumenationen'] = array();
        $sql = "SELECT *
          FROM archiv_iid.Dokumentationen
          WHERE Arbeiten_idArbeiten=".$this->id;
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $this->data['Dokumenationen'][] = array(
            'id'=>$row2['idDokumentationen'],
            'Name'=>$row2['Name'],
            'Pfad'=>$row2['Pfad'],
            'Klassierung'=>$row2['Klassierung'],
          );
        }
        $rs->Close();

        $this->data['Benutzer'] = array();
        $sql = "SELECT b.*, r.Name as Rolle
          FROM archiv_iid.Benutzer b, archiv_iid.BenutzerProArbeit bpa, archiv_iid.Rollen r
          WHERE r.idRollen=bpa.Rollen_idRollen AND b.idBenutzer=bpa.Benutzer_idBenutzer AND bpa.Arbeiten_idArbeiten=".$this->id."
          ORDER BY b.Nachname ASC, b.Vorname ASC";
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $b = array( 'id'=>$row2['idBenutzer']
              , 'Rolle'=>$row2['Rolle']
              , 'Vorname'=>$row2['Vorname']
              , 'Nachname'=>$row2['Nachname']
              , 'Email'=>$row2['Email']
              , 'Jahrgang'=>$row2['Jahrgang']
              , 'Geschlecht'=>$row2['Geschlecht']
          );
          $this->data['Benutzer'][$row2['idBenutzer']] = $b;
        }
        $rs->Close();
        $this->data['Klassierungen'] = array();
        $sql = "SELECT k.Name as Name
          FROM archiv_iid.Klassierungen k, archiv_iid.KlassierungenProArbeit kpo
          WHERE k.idKlassierungen=kpo.Klassierungen_idKlassierungen AND kpo.Arbeiten_idArbeiten=".$this->id;
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $this->data['Klassierungen'][] = $row2['Name'];
        }
        $rs->Close();
        $this->data['Mittel'] = array();
        $sql = "SELECT m.Name as Name
          FROM archiv_iid.Mittel m, archiv_iid.MittelProArbeit mpo
          WHERE m.idMittel=mpo.Mittel_idMittel AND mpo.Arbeiten_idArbeiten=".$this->id;
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $this->data['Mittel'][] = $row2['Name'];
        }
        $rs->Close();
        $this->data['Stati'] = array();
        $sql = "SELECT s.Name as Name
          FROM archiv_iid.Stati s, archiv_iid.StatiProArbeit spo
          WHERE s.idStati=spo.Stati_idStati AND spo.Arbeiten_idArbeiten=".$this->id;
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $this->data['Stati'][] = $row2['Name'];
        }
        $rs->Close();
        $this->data['Studiensemester'] = array();
        $sql = "SELECT s.Name as Name
          FROM archiv_iid.Studiensemester s, archiv_iid.StudiensemesterProArbeit spo
          WHERE s.idStudiensemester=spo.Studiensemester_idStudiensemester AND spo.Arbeiten_idArbeiten=".$this->id;
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $this->data['Studiensemester'][] = $row2['Name'];
        }
        $rs->Close();
        $this->data['Techniken'] = array();
        $sql = "SELECT t.Name as Name
          FROM archiv_iid.Techniken t, archiv_iid.TechnikenProArbeit tpo
          WHERE t.idTechniken=tpo.Techniken_idTechniken AND tpo.Arbeiten_idArbeiten=".$this->id;
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row2 ) {
          $this->data['Techniken'][] = $row2['Name'];
        }
        $rs->Close();
    }

    public function getData() {
        return $this->data;
    }

    public function getID() {
        return $this->idprefix.'-'.sprintf( "%06u", $this->id );
    }

	public function getOriginalID() {
		return $this->id;
	}

    public function getType() {
		return "work";
	}

    public function getEmbedded() {
		return true;
	}

    public function getSource() {
        return 'IIDWork';
    }

	public function getLocations() {
		return array( 'Mediathek:online' );
	}

	public function getOpenAccess() {
		return false;
	}

    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['Titel'] );

        return $title;
    }

    public function getPublisher() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return "Institut Industrial Design HGK FHNW";
    }

    public function getCodes() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		$codes = array();
		return $codes;
    }

    public function getYear() {
        return $this->data['Modul']['Jahr'];
    }

    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return intval( $this->data['Modul']['Jahr'] ) <= 2014 ? 'Aarau':'Basel';
    }

	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        $this->tags = array();

        foreach( array( /* 'Klassierungen', */ 'Mittel' /*, 'Stati' */, 'Studiensemester', 'Techniken' ) as $type ) {
      		foreach( $this->data[$type] as $name ) {
      			$this->tags[] = 'index:'.$type.'/'.md5( trim( $name) ).'/'.trim( $name );
      		}
        }
        foreach( $this->data['Benutzer'] as $b ) {
          if( !intval( $b['Jahrgang'])) continue;
          $t = 'Jahrgang '.$b['Jahrgang'];
          $this->tags[] = 'index:Jahrgang/'.md5( trim( $t) ).'/'.trim( $t );
        }
        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
              $ts = explode( '/', $tag );
              $this->cluster[] = $ts[count( $ts )-1];
        }
        $this->cluster = array_unique( $this->cluster );

        return $this->tags;
	}


    public function getCluster() {
        if( $this->cluster == null) $this->getTags();
        return $this->cluster;
    }

    public function getSignatures() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        return array();
    }

    public function getAuthors() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $authors = array();
    		foreach( $this->data['Benutzer'] as $b ) {
    			$authors[] = trim( $b['Nachname'].', '.$b['Vorname'] );
    		}
		    return $authors;
    }

    public function getLoans() {
        return array();
    }

    public function getBarcode() {
        return null;
    }

    public function getSignature() {
        return null;
    }

    public function getLicenses() {
      $licenses = array( 'unknown' );
		    return $licenses;
    }

    public function getURLs() {
  		$urls = array();
  		return $urls;
    }

    public function getSys() {
        return $this->id;
    }

    public function getMeta() {
        return json_encode( $this->data );
    }

    public function getOnline() {
        return true;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return $this->data['Abstract'];
    }

   public function getContent() {
     if( $this->data == null ) throw new \Exception( "no entity loaded" );
     return $this->data['TextZurArbeit'];
   }

    public function getMetaACL() { return array( 'mediathek/iid', 'global/admin' ); }
    public function getContentACL() { return array( 'mediathek/iid', 'global/admin' ); }
    public function getPreviewACL() { return array(); }
    public function getLanguages() { return array(); }
    public function getIssues() { return array( $this->data['Modul']['Kennziffer'].' - '.$this->data['Modul']['Titel']); }

	public function getCategories() {
		$categories = parent::getCategories();
		$categories[] = 'hgkarchives!!IID';
		return $categories;
	}

	public function getCatalogs() { return array( $this->getSource(), 'HGK-Institute' ); }

}

?>
