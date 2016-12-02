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

class SOLR {
    private $solr = null;
    
    function __construct( \Solarium\Client $solr ) {
        $this->solr = $solr;    
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
        return $result;
    }
	
	public function delete( $id ) {
		$update = $this->solr->createUpdate();
        $update->addDeleteByID($id);
        $update->addCommit();
        $result = $this->solr->update( $update );
        
        echo 'Delete query for '.$id.' executed'."\n";
        echo 'Query status: ' . $result->getStatus()."\n";
        echo 'Query time: ' . $result->getQueryTime()."\n";
	}
	
    public function import( SOLRSource $src, $commit = false ) {
        $id = $src->getID();
		$this->delete( $id );

        $update = $this->solr->createUpdate();
        $helper = $update->getHelper();
        $doc = $update->createDocument();
        
        $doc->id = $src->getID();
        $doc->setField( 'originalid', $src->getOriginalID());
        $doc->setField( 'source', $src->getSource());
        $doc->setField( 'type', $src->getType());
        $doc->setField( 'openaccess', $src->getOpenAccess());
		foreach( $src->getLocations() as $loc )
			$doc->addField( 'location', $loc );
        $doc->setField( 'title', /* utf8_encode */( $src->getTitle()));
		
		$publisher = $src->getPublisher();
		if( !is_array( $publisher )) $publisher = array( $publisher );
		foreach( $publisher as $pub )
			$doc->addField( 'publisher', $pub );

		$doc->setField( 'city', /* utf8_encode */( $src->getCity()));
        $doc->setField( 'year', $src->getYear());
        $doc->setField( 'abstract', $src->getAbstract());
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
            $doc->setField( 'metatext', ( $meta ));
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
        foreach( SOLR::buildTag($src->getCategories(), '!!' ) as $category )
                $doc->addField( 'category', /* utf8_encode */( $category ));
        foreach( $src->getCluster() as $cluster )
            $doc->addField( 'cluster', $cluster );
        foreach( $src->getLoans() as $loan )
            $doc->addField( 'loan', $loan );
        foreach( $src->getLicenses() as $license )
            $doc->addField( 'license', /* utf8_encode */( $license ));
        foreach( $src->getSignatures() as $sig )
            $doc->addField( 'signature', $sig );
        foreach( $src->getURLs() as $url )
            $doc->addField( 'url', $url);
        foreach( $src->getCodes() as $code )
            $doc->addField( 'code', $code);
        foreach( $src->getIssues() as $issue )
            $doc->addField( 'issue', $issue);
        foreach( $src->getLanguages() as $lang )
            $doc->addField( 'lang', $lang);
        
        $doc->setField( 'online', $src->getOnline());
        $doc->setField( 'embedded', $src->getEmbedded());
        
		$doc->setField( 'creation_date', gmdate('Y-m-d\TH:i:s\Z', time()));
		
        $update->addDocuments( array( $doc ));
        if( $commit )
            $update->addCommit();
        $result = $this->solr->update( $update );

        echo 'Insert query for '.$id.' executed'."\n";
        echo 'Query status: ' . $result->getStatus()."\n";
        echo 'Query time: ' . $result->getQueryTime()."\n";
        
    }
}    
?>
