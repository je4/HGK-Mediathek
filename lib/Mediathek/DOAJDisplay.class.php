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
 * @subpackage  NEBISDisplay
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

class DOAJDisplay extends DisplayEntity {
    private $metadata;
    
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );
        
        $this->metadata = (array) json_decode( $this->data );
    }
   
    public function getSchema() {
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array( 'Periodical' );
		$schema['@id'] = $this->doc->id;
		$schema['name'] = $this->doc->title;
		if( isset( $this->doc->code )) foreach( $this->doc->code as $c )
			if( preg_match( '/ISSN:(.*)$/', $c, $matches ))
				$schema['issn'] = $matches[1];
		
		if( isset( $this->doc->author_ss ) && count( $this->doc->author_ss )) {
			$schema['author'] = array();
			foreach( $this->doc->author_ss as $author ) {
				$schema['author'][] = array( '@type' => 'Person', 'name' => $author );
			}
		}
		
		if( $this->doc->publisher && count( $this->doc->publisher ))
			foreach( $this->doc->publisher as $publisher ) {
				$schema['publisher'][] = array( '@type' => 'Organization', 'legalName' => $publisher );
			}
		$schema['url'] = array( 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode( $this->doc->id ));	
		if( isset( $this->doc->url )) {
			foreach( $this->doc->url as $url ) {
				if( preg_match( '/^[^:]+:(.*)$/', $url, $matches ))
				$schema['url'][] = $matches[1];
			}
		}
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );
		
		$schema['license'] = implode( '; ', $this->doc->license );
		
		return $schema;
	}
     
	public function detailView() {
        global $config, $googleservice, $googleclient;
        $html = '';

		return $html;
	}
	
    public function desktopList() {
		$html = '';
		$idr = preg_replace( '/[^a-zA-Z0-9]/', '_', $this->doc->id );
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $idr; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $idr; ?>">
                    <?php echo htmlspecialchars( implode( ';', $this->doc->publisher )); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="fa fa-newspaper-o" aria-hidden="true"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $idr; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $idr; ?>">
                    <?php echo htmlspecialchars( $this->doc->title ); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $idr; ?>">
				<?php
					if( isset( $this->doc->language )) echo "Sprache: ".implode( '; ', $this->doc->language )."<br />\n";
					if( isset( $this->doc->license )) echo "Lizenz: ".implode( '; ', $this->doc->license )."<br />\n";
					if( isset( $this->doc->cluster_ss )) echo "Subject: ".implode( '; ', $this->doc->cluster_ss )."<br />\n";
					if( isset( $this->doc->code )) foreach( $this->doc->code as $code  ) echo str_replace( ':', ': ', $code )."<br />\n";
					foreach( $this->doc->url as $url ) {
						if( preg_match( '/^([^:]+):(.*)$/', $url, $matches ))
						$t = $matches[1];
						$t{0} = strtoupper( $t{0} );
						echo $t.': <a style="word-break: break-all;" href="'.$matches[2].'" target="_blank">'.$matches[2].'</a><br />'."\n";
					}
				?>
                    <?php if( isset( $this->metadata['Full text formats'] )) echo 'Volltextformate: '.htmlspecialchars( $this->metadata['Full text formats'] )."<br />\n"; ?>
                    <?php if( isset( $this->metadata['Full text language'] )) echo 'Sprache: '.htmlspecialchars( $this->metadata['Full text language'] )."<br />\n"; ?>
                    <?php if( isset( $this->metadata['Journal license'] )) echo 'Lizenz: '.htmlspecialchars( $this->metadata['Journal license'] )."<br />\n"; ?>
                    <?php if( isset( $this->metadata['Journal ISSN (print version)'] )) echo 'ISSN: '.htmlspecialchars( $this->metadata['Journal ISSN (print version)'] )."<br />\n"; ?>
                    <?php if( isset( $this->metadata['Journal EISSN (online version)'] )) echo 'EISSN: '.htmlspecialchars( $this->metadata['Journal EISSN (online version)'] )."<br />\n"; ?>
                    <?php if( isset( $this->metadata['Journal URL'] )) echo 'URL: <i class="fa fa-external-link" aria-hidden="true"></i><a href="'.urlencode($this->metadata['Journal URL'].'" target="_blank">'.htmlspecialchars( $this->metadata['Journal URL'] )."</a><br />\n"; ?>
					
					ID: <span style="word-break: break-all;"><?php echo $this->doc->id; ?></span>
					<p>
					<!-- <a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>">Detail</a><br />
					-->
                    
                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}