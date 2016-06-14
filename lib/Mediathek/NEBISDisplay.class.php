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

class NEBISDisplay extends DisplayEntity {
    
    public function __construct( $doc, $urlparams, $db ) {
        parent::__construct( $doc, $urlparams, $db );

        $this->db = $db;
    }
    
	public function detailView() {
        global $config, $googleservice, $googleclient;
        $html = '';
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>		
		<div style="background:  #f5f2f0;">
			<div class="row">
				<div class="col-md-12">
				<h4>Google Books</h4>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="card-columns">
<?php

$search = new ContextSearchGoogle( $this->doc, $this->db, 'book');


echo $search->desktopCards();
?>

					</div>
				</div>
			</div>
		</div>

<?php
        $html .= ob_get_contents();
        ob_end_clean();
		return $html;
	}
	
    public function desktopList() {
        global $config, $googleservice, $googleclient;
        $html = '';
		
        $cfg = array(
                   'indent'         => true,
                   'output-xml'   => true,
                   'input-xml' => true,
                   'wrap'           => 150);
        
		$entity = new MarcEntity($this->db);
		$entity->loadFromXML( $this->data, substr( $this->doc->id, strlen( $this->doc->source)), $this->doc->source );
		
        // Tidy
        $tidy = new \Tidy;
        $tidy->parseString($this->data, $cfg, 'utf8');
        $tidy->cleanRepair();
         //echo "<!--\n".$tidy."\n-->\n";
        
        // Output
//        echo $tidy;

        $authors = array_unique( $entity->getAuthors());
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
					<?php if( count( $authors )) echo htmlspecialchars( $authors[0]  ); 
					 if( count( $authors ) > 1) echo " et al."; ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="fa fa-book"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $entity->getTitle() ); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $this->doc->id; ?>">
<?php			
				for( $i = 1; $i < count( $authors ); $i++ ) {
					echo ($i>1?"; ":"").htmlspecialchars( $authors[$i] );
				}
?>					
				
<?php			
				if( count( $authors ) > 1 ) echo "<br />\n";
				$publisher = $entity->getPublisher();
				$city = $entity->getCity();
				$year = $entity->getYear();
				if( $city ) echo htmlspecialchars( $city ).': '; 
				if( $publisher ) echo htmlspecialchars( $publisher ).', ';
				if( $year ) echo htmlspecialchars( $year ).'.';
				if( $city || $year || $publisher ) echo "<br />\n";

				$codes = $entity->getCodes();
				foreach( $codes as $code ) 
					echo htmlspecialchars( $code )."<br />\n";
				$signatures = $this->doc->signature;
				if( is_array( $signatures )) foreach( $signatures as $sig ) 
					if( substr( $sig, 0, 10 ) == 'nebis:E75:' ) echo 'Signatur: '.htmlspecialchars( substr( $sig, 10 ))."<br />\n";
				$locations = $entity->getLocations();
				foreach( $locations as $loc ) 
				if( substr( $loc, 0, 4 ) == 'E75:' ) echo 'Standort: Regal <b>'.$loc{10}.'</b> Kiste <b>'.htmlspecialchars( str_replace( '_', '', substr( $loc, 12 ))).' <a style="padding: 0px;" href="#" class="btn btn-default" data-toggle="modal" data-target="#3DModal" data-3D="3d.iframe.php?box='.urlencode(substr( $loc, 10 )).'" ><i class="fa fa-street-view" aria-hidden="true"></i></a></b><br />'."\n";
				$notes = $entity->getGeneralNote();
				foreach( $notes as $note ) 
					echo 'Anmerkung: '.htmlspecialchars( $note )."<br />\n";
				
				if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
					$us = explode( ':', $u );
					if( substr( $us[1], 0, 4 ) == 'http' ) {
						$url = substr( $u, strlen( $us[0])+1 );
						echo "{$us[0]}: <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i><a href=\"{$url}\">{$url}</a><br />\n";
					}
				}
				
				echo "ID: ".$this->doc->id."<br />\n";
?>				
<?php			
?>				
				<p />
					<!-- <a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>">Detail</a><br /> -->
                    <!-- <pre><?php echo ( htmlspecialchars( $tidy )); ?></pre> -->
                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}