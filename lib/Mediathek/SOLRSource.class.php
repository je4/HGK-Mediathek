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
 * @subpackage  SOLRSource
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

include dirname(__FILE__).'/../CiteProc.inc.php';


abstract class SOLRSource {
	protected $data = null;
	protected $id = null;
	private static $cite = array();

	public function reset() {
		$this->data = null;
		$this->id = null;
	}

	abstract public function loadFromDoc( $doc );
	abstract public function getID();
	abstract public function getOriginalID();
    abstract public function getSource();
    abstract public function getType();
	abstract public function getOpenAccess();
	abstract public function getLocations();
    abstract public function getTitle();
    abstract public function getCodes();
    abstract public function getAbstract();
    abstract public function getContent();
    abstract public function getTags();
    abstract public function getSignatures();
    abstract public function getAuthors();
    abstract public function getLoans();
    //abstract public function getBarcode();
    abstract public function getLicenses();
    abstract public function getURLs();
    abstract public function getSys();
    abstract public function getPublisher();
    abstract public function getYear();
    abstract public function getCity();
    abstract public function getCluster();
    abstract public function getMeta();
    abstract public function getOnline();
	abstract public function getEmbedded();
    abstract public function getMetaACL();
    abstract public function getContentACL();
    abstract public function getPreviewACL();
	abstract public function getLanguages();
	abstract public function getIssues();
	public function getCategories() {
		$rm = new \ReflectionMethod(get_called_class(), 'getSource');
		return array( 'source!!'.$rm->invoke($this) );
	}

	public function getSourceIDs() {
		return array();
	}

	abstract public function getCatalogs();
/*	{
		$rc = new \ReflectionClass(get_called_class());
		if( $rc->hasMethod( 'getCatalogs' )) {
			echo get_called_class().'::getCatalogs()'."\n";
			//$rm = $rc->getMethod( 'getCatalogs' );
			$rm = new \ReflectionMethod(get_called_class(), 'getCatalogs');
			return $rm->invoke( $this );
		}
		else {
			return array( $this->getSource() );
		}

	}
*/

public function getCSL() {
		return array();
}

public function cite( $style, $lang, $mode ) {
      global $config;
      if( !array_key_exists( $style.$lang, self::$cite )) {
        $csl_style = file_get_contents( $config['zotero']['csl_styles'].'/'.$style );
        self::$cite[$style.$lang] = new \citeproc( $csl_style );
      }

      $csl_json = $this->getCSL();
      return self::$cite[$style.$lang]->render( (object)$csl_json , $mode );
    }

}

?>
