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
 * @subpackage  SOLR
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
 * Communication with SOLR instance
 *
 */

/*
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Esource%3Adegruyter%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Esource%3Aeartnet%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Esource%3ADOAB%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Edeleted%3Atrue%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Ecatalog%3AKaskoArchiv%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Esource%3Adiplomhgk%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Esource%3Agrenzgang%3C%2Fquery%3E%3C%2Fdelete%3E
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Cdelete%3E%3Cquery%3Eid%3Azotero-2171465.*%3C%2Fquery%3E%3C%2Fdelete%3E

http://localhost:8983/solr/cataloguecoll/uidstream.body=%3Cdelete%3E%3Cquery%3Eid%diplomhgk-2019*%3C%2Fquery%3E%3C%2Fdelete%3E


"source":"DOAB"
http://localhost:8983/solr/cataloguecoll/update?stream.body=%3Ccommit/%3E
*/

class SOLR {
    private $solr = null;
    private $db = null;

    function __construct( \Solarium\Client $solr, $db ) {
        $this->solr = $solr;
        $this->db = $db;
    }

    static public function buildTag( $tags, $divider = '/' ) {
        $result = array();
        foreach( $tags as $tag ) {
            $tlist = explode($divider, $tag );
            for( $i = 0; $i < count( $tlist ); $i++ ) {
                $r = $i;
                for( $j = 0; $j <= $i; $j++ )
                    $r .= $divider.trim( $tlist[$j] );
                if( array_search( $r, $result ) === false )
                    $result[] = $r;
            }
        }
        return array_unique( $result );
    }

	public function delete( $id, $commit = false ) {
		$query = $this->solr->createSelect();
		$helper = $query->getHelper();
    echo "Trying to delete {$id}\n";
		$query->setQuery( 'id:'.$helper->escapeTerm( $id ));
		$resultset = $this->solr->select( $query );
		if( $resultset->getNumFound() == 1 ) {
			foreach( $resultset->getDocuments() as $doc ) {
				$update = $this->solr->createUpdate();
				$fields = $doc->getFields();
				$flds2 = array();
				foreach( $fields as $key=>$val ) {
					switch( $key ) {
						case 'score':
						case '_version_':
							break;
						default:
							$flds2[$key] = $val;
					}
				}
				$udoc = $update->createDocument( $flds2 );
				$udoc->setField( 'creation_date', gmdate('Y-m-d\TH:i:s\Z', time()));
				$udoc->setField( 'deleted', true );
				$update->addDocuments( array( $udoc ));
				if( $commit ) $update->addCommit();
				$result = $this->solr->update( $update );
				echo 'Delete query for '.$id.' executed'."\n";
				echo 'Query status: ' . $result->getStatus()."\n";
				echo 'Query time: ' . $result->getQueryTime()."\n";
				break;
			}
		}
    else {
        echo "    not found...\n";
    }
	}



    public function import( SOLRSource $src, $commit = false ) {

        echo 'import...';

        $id = $src->getID();

		//$this->delete( $id );
        $update = $this->solr->createUpdate();
        $helper = $update->getHelper();
        $doc = $update->createDocument();

        $doc->id = $src->getID();
        $doc->setField( 'originalid', $src->getOriginalID());
        $doc->setField( 'source', $src->getSource());
		$doc->setField( 'type', strtolower( $src->getType()));

//    if( $id == 'swissbib-367224763') echo "getType() ok\n";

        $doc->setField( 'openaccess', $src->getOpenAccess());
		foreach( $src->getLocations() as $loc ) {
			$doc->addField( 'location', $loc );
		}
		
		if( method_exists( $src, 'getMediaTypes' )) foreach( $src->getMediaTypes() as $mt ) {
			$doc->addField( 'mediatype', $mt );
		}


        $doc->setField( 'title', /* utf8_encode */( $src->getTitle()));

		$publisher = $src->getPublisher();
		if( !is_array( $publisher )) $publisher = array( $publisher );
		foreach( $publisher as $pub )
			$doc->addField( 'publisher', $pub );

		$doc->setField( 'city', /* utf8_encode */( $src->getCity()));
        $doc->setField( 'year', $src->getYear());
        $h = $src->getAbstract();
        if( $h != null ) $doc->setField( 'abstract', $h );
        $h = $src->getContent();
        if( $h != null ) $doc->setField( 'content', $h );
        $meta = $src->getMeta();
			if( $meta == null ) {
				echo "no meta error\n";
			}
        if( $meta != null ) {
//			print_r( $meta );
			$metagz = @gzencode( $meta );
			//echo $metagz;
			if( $metagz === false ) {
				echo "gzencode error\n";
			}
            $doc->setField( 'metagz', base64_encode( $metagz ));
            //$doc->setField( 'metatext', ( $meta ));
		}
		foreach( $src->getMetaACL() as $acl )
           $doc->addField( 'acl_meta', $acl );
        foreach( $src->getContentACL() as $acl )
           $doc->addField( 'acl_content', $acl );
        foreach( $src->getPreviewACL() as $acl )
           $doc->addField( 'acl_preview', $acl );
        foreach( $src->getAuthors() as $author )
           $doc->addField( 'author', /* utf8_encode */($author ));
        foreach( /* SOLR::buildTag */($src->getTags()) as $tag )
                $doc->addField( 'tag', /* utf8_encode */( $tag ));


        $categories = $src->getCategories();
        // add curated categories
        $found = false;
        $sql = "SELECT category FROM curate_category WHERE id=".$this->db->qstr( $id );
        $rs = $this->db->Execute( $sql );
        foreach($rs as $row ) {
            $categories[] = $row['category'];
            if( $src->getOnline()) {
              $categories[] = $row['category'].'!!Online';
              $found = true;
            }
            foreach( $src->getLocations() as $loc ) {
              if( preg_match( '/^NEBIS:E75:([A-Z])_[0-9]{3}_[ab]/i', $loc, $matches )) {
                $categories[] = $row['category'].'!!Regal '.$matches[1];
                $found = true;
              }
        		}
        }
        $rs->Close();
        $categories = array_unique( $categories );
        if( $found ) {
          $key = array_search( 'area!!unknown', $categories );
          if( $key !== false ) unset( $categories[$key] );
        }

        foreach( SOLR::buildTag($categories, '!!' ) as $category )
                $doc->addField( 'category', /* utf8_encode */( $category ));
        foreach( Helper::clearCluster( $src->getCluster()) as $cluster )
            $doc->addField( 'cluster', $cluster );
        foreach( $src->getLoans() as $loan )
            $doc->addField( 'loan', $loan );
        foreach( $src->getLicenses() as $license )
            $doc->addField( 'license', /* utf8_encode */( $license ));
        foreach( $src->getSignatures() as $sig )
            $doc->addField( 'signature', $sig );
        foreach( $src->getURLs() as $url )
            $doc->addField( 'url', $url);
        $codes = array();
        foreach( $src->getCodes() as $code ) {
        	if( preg_match( '/(EISBN|ISBN|ISSN):(.*)$/', $code, $matches )) {
            $nr = preg_replace( '/[^0-9X]/', '', $matches[2] );
            if( $matches[1] == 'ISBN' || $matches[1] == 'EISBN' ) $nr = Helper::isbn13( $nr );
            $codes[] = $matches[1].':'.$nr;
        	}
          else {
            $codes[] = $code;
          }
        }

        $codes = array_unique( $codes );
        $codeDB = count($codes) > 1;
        global $db;
        foreach( $codes as $code ) {
          $doc->addField( 'code', $code);
          list( $type, $val ) = explode( ':', $code );
          if( $codeDB ) if( preg_match( '/e?isbn/i', $type )) {
            $sql = "INSERT INTO enrich_isbn( id, type, code )
              VALUES(
                ".$db->qstr( $src->getID())."
                ,".$db->qstr( $type )."
                ,".$db->qstr( $val )."
              )";
              try{
                $db->Execute( $sql );
              }
              catch( \Exception $ex ) {
                //echo $ex->getMessage()."\n";
              }
            }
          }
        foreach( $src->getCatalogs() as $cat )
            $doc->addField( 'catalog', $cat);
        foreach( $src->getIssues() as $issue ) {
        	if( strlen( trim( $issue ))) {
        		$doc->addField( 'issue', trim( $issue ));
        	}
        }
        foreach( $src->getLanguages() as $lang )
            $doc->addField( 'lang', $lang);
        foreach( $src->getSourceIDs() as $sid)
           	$doc->addField( 'sourceid', $sid);

        $doc->setField( 'online', $src->getOnline());
        $doc->setField( 'embedded', $src->getEmbedded());

		    $doc->setField( 'creation_date', gmdate('Y-m-d\TH:i:s\Z', time()));


        $update->addDocuments( array( $doc ));
        if( $commit )
            $update->addCommit();
        $result = $this->solr->update( $update );

        echo 'Insert query for '.$id.' executed'."\n";
//        echo 'Query status: ' . $result->getStatus()."\n";
//        echo 'Query time: ' . $result->getQueryTime()."\n";

    }
}
?>
