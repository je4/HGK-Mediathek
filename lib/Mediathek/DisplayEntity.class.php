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
 * @subpackage  DisplayEntity
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

abstract class DisplayEntity {
    protected $data = null;
    protected $doc = null;
	protected $urlparams = null;
	protected $db = null;
	protected $highlight = null;
	protected $qobj = null;
    public function __construct( $doc, $urlparams, $db, $highlightedDoc  ) {
        $this->doc = $doc;
		$this->urlparams = $urlparams;
		$this->db = $db;
        $this->data = gzdecode( base64_decode( $doc->metagz ));
 //       echo "<!--\n".$this->data."\n-->\n";
		$this->highlight = $highlightedDoc;
    }

    public function getHeading() {
  		$html = '';

          ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
  ?>
  		                    <h2 class="small-heading">Mediathek</h2>

  					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
  <?php
          $html .= ob_get_contents();
          ob_end_clean();
  		return $html;
  	}

    public function getMainHeading() {
      $html = '';

          ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
  ?>
  <div class="image-container col-md-1 col-sm-12">
      <div class="mask">
      </div>
      <div style="left: 20%;" class="main-heading">
          <h1>Detail</h1>
      </div>
  </div>
  <?php
          $html .= ob_get_contents();
          ob_end_clean();
      return $html;
    }



	public function getCatalogList() {
		global $config;

		if( !$this->qobj ) {
			$this->qobj = Helper::readQuery( $this->urlparams['q'] );
		}

		$js_catlist = '';
		$ds = $config['defaultcatalog'];
		if( @is_array( $this->qobj['facets']['catalog'] ))
		{
			foreach( $this->qobj['facets']['catalog'] as $cat ) {
				$ds[] = $cat;
			}
		}
		$ds = array_unique( $ds );
		foreach( $ds as $src )
			$js_catlist .= ",'".trim( $src )."'";
		$js_catlist = trim( $js_catlist, ',');
		return $js_catlist;
	}

	public function getSourceList() {
		global $config;

		if( !$this->qobj ) {
			$this->qobj = Helper::readQuery( $this->urlparams['q'] );
		}

		$js_catlist = '';
		$ds = $config['defaultsource'];
		if( is_array( $this->qobj['facet']['source'] ))
		{
			foreach( $this->qobj['facet']['source'] as $cat ) {
				$ds[] = $cat;
			}
		}
		$ds = array_unique( $ds );
		foreach( $ds as $src )
			$js_catlist .= ",'".trim( $src )."'";
		$js_catlist = trim( $js_catlist, ',');
		return $js_catlist;
	}

  public static function createToken( $sub, $validity=7200 ) {
    global $config;
    // next full hour
    $exp = (time()+$validity+3600);
    $exp -= $exp % 3600;
    $payload = ['sub'=>strtolower( $config['mediaserver']['sub_prefix'].$sub ), 'exp'=>$exp];
    //print_r( $payload );
    $token = jwt_encode( $payload, $config['mediaserver']['key'] );
    return $token;
  }

  public static function mediaLink( $url ) {
    global $config;
      if( preg_match( '/^mediaserver:([^\/]+\/[^\/]+\/[^\/]+)(\/[^\/]*)?$/', $url, $matches )) {
        $paramstring = '';
        if( isset( $matches[2] )) {
          $ps = explode( '/', strtolower( trim( $matches[2], '/' )));
          sort( $ps );
          $paramstring = '/'.implode( '/', $ps );
        }
        $link = $config['mediaserver']['baseurl'].$matches[1].$paramstring.'?token='.self::createToken( $matches[1].$paramstring );
        return $link;
      }
      return null;
   }

	public abstract function getSchema( );
    public abstract function detailView();
    public abstract function desktopList();
}
