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
 * @subpackage  Zotero
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

use \Httpful\Request;

class Zotero {
  var $db;
  var $apikey;
  var $apiurl;
  var $contentpath;

  function __construct( $db, $apiurl, $apikey, $contentpath ) {
    $this->db = $db;
    $this->apiurl = $apiurl;
    $this->apikey = $apikey;
    $this->contentpath = $contentpath;
  }

  function syncGroup( $id ) {
    $uri = $this->apiurl.'/groups/'.$id;
    $response = Request::get( $uri )
      ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
      ->send();
  //    echo( $response );
      $g = json_decode( $response, true );
      $sql = "REPLACE INTO zotero.groups( `id`, name, type, numItems, dateAdded, dateModified, data ) VALUES(
        {$g['id']}
        , ".$this->db->qstr( $g['data']['name'])."
        , ".$this->db->qstr( $g['data']['type'])."
        , ".$this->db->qstr( $g['meta']['numItems'])."
        , ".$this->db->qstr( $g['meta']['created'])."
        , ".$this->db->qstr( $g['meta']['lastModified'])."
        , ".$this->db->qstr( $response )."
         )";
//      echo $sql;
     $this->db->Execute( $sql );
  }

  function syncItems( $groupid ) {
    $this->syncGroup( $groupid );
    $sql = "SELECT numItems FROM zotero.groups WHERE id={$groupid}";
    $numItems = intval( $this->db->getOne( $sql ));

    $start = 0;
    $limit = 100;
    while( $start < $numItems ) {
      $uri = "{$this->apiurl}/groups/{$groupid}/items?limit={$limit}&start={$start}";
      $response = Request::get( $uri )
        ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
        ->send();

      $items = json_decode( $response, true );
      foreach( $items as $i ) {
        $sql = "REPLACE INTO zotero.items( `key`, libraryid, parentKey, itemType, linkMode, title, dateAdded, dateModified, data ) VALUES (
          ".$this->db->qstr( $i['key'] )."
          , ".$this->db->qstr( $i['library']['id'] )."
          , ".@$this->db->qstr( $i['data']['parentItem'] )."
          , ".$this->db->qstr( $i['data']['itemType'] )."
          , ".@$this->db->qstr( $i['data']['linkMode'] )."
          , ".@$this->db->qstr( $i['data']['title'] )."
          , ".$this->db->qstr( $i['data']['dateModified'] )."
          , ".$this->db->qstr( $i['data']['dateAdded'] )."
          , ".$this->db->qstr( $response )."
          )";
        $this->db->Execute( $sql );

        $sql = "DELETE FROM zotero.creators2items WHERE itemKey=".$this->db->qstr( $i['key'] );
        $this->db->Execute( $sql );
        if( @is_array( $i['data']['creators'] )) foreach( $i['data']['creators'] as $c ) {

          $sql = "SELECT id FROM zotero.creators
            WHERE ".(array_key_exists( 'lastName', $c ) ? "lastName LIKE ".$this->db->qstr( $c['lastName'] )." AND firstName LIKE ".$this->db->qstr( $c['firstName'] ) : "name LIKE ".$this->db->qstr( $c['name'] ));
          $id = intval( $this->db->getOne( $sql ));
          if( !$id ) {
            if( array_key_exists( 'lastName', $c )) $sql = "INSERT INTO zotero.creators ( lastName, firstName ) VALUES (".$this->db->qstr( $c['lastName'] ).", ".$this->db->qstr( $c['firstName'] ).")";
            else $sql = "INSERT INTO zotero.creators ( name ) VALUES (".$this->db->qstr( $c['name'] ).")";
            $this->db->Execute( $sql );
            $id = $this->db->insert_Id();
          }
          $sql = "INSERT INTO zotero.creators2items( itemKey, libraryId, role, creatorsId ) VALUES(
            ".$this->db->qstr( $i['key'] )."
            , {$i['library']['id']}
            , ".$this->db->qstr( $c['creatorType'] )."
            , {$id}
          );";
          $this->db->Execute( $sql );
        }
        $sql = "DELETE FROM zotero.items2collections WHERE itemKey=".$this->db->qstr( $i['key'] );
        $this->db->Execute( $sql );
        if( @is_array( $i['data']['collections'] )) foreach( $i['data']['collections'] as $k ) {
          $sql = "INSERT INTO zotero.items2collections( itemKey, libraryId, collectionsKey ) VALUES(
              ".$this->db->qstr( $i['key'] )."
              , {$i['library']['id']}
            , ".$this->db->qstr( $k )."
          );";
          $this->db->Execute( $sql );
        }
      }
      $start += $limit;
    }
  }
}
