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
    
    static public function buildTag( $tags ) {
        $result = array();
        foreach( $tags as $tag ) {
            $tlist = explode('/', $tag );
            for( $i = 0; $i < count( $tlist ); $i++ ) {
                $r = $i;
                for( $j = 0; $j <= $i; $j++ )
                    $r .= '/'.trim( $tlist[$j] );
                if( array_search( $r, $result ) === false )
                    $result[] = $r;
            }
        }
        return $result;
    }
    
    public function import( SOLRSource $src, $commit = false ) {
        $id = $src->getID();
        $update = $this->solr->createUpdate();
        $update->addDeleteByID($id);
        $update->addCommit();
        $result = $this->solr->update( $update );
        
        echo 'Delete query for '.$id.' executed'."\n";
        echo 'Query status: ' . $result->getStatus()."\n";
        echo 'Query time: ' . $result->getQueryTime()."\n";

        $update = $this->solr->createUpdate();
        $helper = $update->getHelper();
        $doc = $update->createDocument();
        
        $doc->id = $src->getID();
        $doc->setField( 'source', $src->getSource());
        $doc->setField( 'openaccess', $src->getOpenAccess());
		foreach( $src->getLocations() as $loc )
			$doc->addField( 'location', $loc );
        $doc->setField( 'title', /* utf8_encode */( $src->getTitle()));
        $doc->setField( 'publisher', /* utf8_encode */( $src->getPublisher()));
        $doc->setField( 'city', /* utf8_encode */( $src->getCity()));
        $doc->setField( 'year', $src->getYear());
        $doc->setField( 'abstract', $src->getAbstract());
        $meta = $src->getMeta();
        if( $meta != null ) {
			$metagz = gzencode( $meta );
			//echo $metagz;
			
            $doc->setField( 'metagz', base64_encode( $metagz ));
            $doc->setField( 'metatext', ( $meta ));
		}
        foreach( $src->getAuthors() as $author )
           $doc->addField( 'author', /* utf8_encode */($author ));
        foreach( /* SOLR::buildTag */($src->getTags()) as $tag )
                $doc->addField( 'tag', /* utf8_encode */( $tag ));
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
        
        $doc->setField( 'online', $src->getOnline());
        
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
