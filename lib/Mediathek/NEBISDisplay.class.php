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
    
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->db = $db;
    }
    
	public function detailView() {
        global $config, $googleservice, $googleclient, $solrclient, $db, $urlparams, $pagesize;
        $html = '';
$cfg = array(
                   'indent'         => true,
                   'output-xml'   => true,
                   'input-xml' => true,
                   'wrap'           => 150);
        
		$entity = new MarcEntity($this->db);
		$entity->loadFromXML( $this->data, substr( $this->doc->id, strlen( $this->doc->source)), $this->doc->source );
		$authors = array_unique( $entity->getAuthors());

		$squery = $solrclient->createSelect();
		$helper = $squery->getHelper();

		
        // Tidy
        $tidy = new \Tidy;
        $tidy->parseString($this->data, $cfg, 'utf8');
        $tidy->cleanRepair();
         //echo "<!--\n".$tidy."\n-->\n";
        
        // Output
//        echo $tidy;
		$kiste = null;
		if( is_array( $this->doc->location )) foreach( $this->doc->location as $loc ) {
		  if( substr( $loc, 0, 10 ) == 'E75:Kiste:' ) {
			  $kiste = substr( $loc, 10 );
			  break;
		  }
		} 

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>		
		<div class="row">
			<div class="col-md-3">
				<div style="">
				<span style="; font-weight: bold;">Aktuelles Buch</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
							<b><?php echo htmlspecialchars( str_replace( '>>', '', str_replace( '<<', '', $entity->getTitle()))); ?></b><br />
							<?php 
							$i = 0;
							foreach( $authors as $author ) { 
								if( $i > 0) echo "; "; ?>
									<a href="javascript:doSearchFull('author:&quot;<?php echo trim( $author ); ?>&quot;', '', [], [], 0, <?php echo $pagesize; ?> );">
										<?php echo htmlspecialchars( $author ); ?>
									</a>
								<?php				
								$i++;
							}
							//echo htmlspecialchars( implode( '; ', $authors )); 
							?>
							
							<br />
<?php
							$publishers = $entity->getPublisher();
							$city = $entity->getCity();
							$year = $entity->getYear();
							if( $city ) echo htmlspecialchars( $city ).': '; 
							if( $publishers ) { 
								foreach( $publishers as $publisher ) { ?>
									<a href="javascript:doSearchFull('publisher:&quot;<?php echo trim( $publisher ); ?>&quot;', '', [], [], 0, <?php echo $pagesize; ?> );">
										<?php echo htmlspecialchars( $publisher ); ?>
									</a>
							<?php 
								}
							}
								
							if( $year ) echo htmlspecialchars( $year ).'.';
							if( $city || $year || $publisher ) echo "<br />\n";

							$codes = $entity->getCodes();
							if( is_array( $codes )) foreach( $codes as $code ) 
								echo htmlspecialchars( str_replace( ':', ': ', $code ))."<br />\n";
							
							$signatures = $this->doc->signature;
							if( is_array( $signatures )) foreach( $signatures as $sig ) 
								if( substr( $sig, 0, 10 ) == 'nebis:E75:' ) echo 'Signatur: <a href="redir.php?id='.urlencode( $this->doc->id ).'&url='.urlencode( 'http://recherche.nebis.ch/primo_library/libweb/action/search.do?fn=search&ct=search&vl(freeText0)='.urlencode( $this->doc->originalid ).'&vid=NEBIS&fn=change_lang&prefLang=de_DE&prefBackUrl=http://recherche.nebis.ch/nebis/action/search.do?fn=search&ct=search&vl(freeText0)='.urlencode( $this->doc->originalid ).'&search=&backFromPreferences=true.' ).'"
										target="_blank">'.htmlspecialchars( substr( $sig, 10 ))."</a><br />\n";

?>							
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div style="">
				<span style="; font-weight: bold;">Raumübersicht</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div class="renderer"></div>
					</div>
				</div>
<?php
//        echo '<pre>'.nl2br( htmlspecialchars( $tidy )).'</pre>';

?>				
			</div>
			<div class="col-md-3">
<?php 	if( is_array( $this->doc->cluster_ss ))  { ?>		
				<div style="">
				<span style="; font-weight: bold;">Themen</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
						foreach( $this->doc->cluster_ss as $cl ) {
//						foreach( $entity->getCluster() as $cl ) {
//							echo htmlspecialchars( $cl ).'<br />';
?>
								<label>
									<a href="javascript:doSearchFull('', '', [], {source: ['NEBIS'], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
								</label><br />
								
							<!-- <div class="checkbox checkbox-green">
								<input class="facet" type="checkbox" id="cluster" value="<?php echo htmlentities($cl); ?>">
								<label for="cluster<?php echo $i; ?>">
									<?php echo htmlspecialchars( $cl ); ?>
								</label>
							</div> -->
<?php							
						}
?>							
					</div>
				</div>
						<?php  } ?>				
<!--				<div style="">
				<span style="; font-weight: bold;">Kontext</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
					</div>
				</div>
				<div style="">
				<span style="; font-weight: bold;">Systematik</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
					</div>
				</div>
			</div>
-->
		</div>
<?php
if( $kiste ) {
?>
		<div class="row">
			<div class="col-md-9">
				<div style="">
				<span style="; font-weight: bold;">Weitere Bücher in Kiste <?php echo $kiste; ?></span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php						
	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$qstr = 'location:'.$helper->escapePhrase( 'E75:Kiste:'.$kiste );
	$squery->setQuery( $qstr );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	$numPages = floor( $numResults / 500 );
	if( $numResults % 500 > 0 ) $numPages++;

	echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".(1)." of {$numPages}) -->\n";

	$res = new DesktopResult( $rs, 0, 500, $db, $urlparams );
	echo $res->getResult();

?>					
					</div>
				</div>
			</div>
			
			<div class="col-md-3">
			</div>
		</div>
<?php } ?>
<!--
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

//$search = new ContextSearchGoogle( $this->doc, $this->db, 'book');


//echo $search->desktopCards();
?>

					</div>
				</div>
			</div>
		</div>
-->
<?php 
  $box = '';
  $boxjson = '';
  if( is_array( $this->doc->location )) foreach( $this->doc->location as $loc ) {
	  if( substr( $loc, 0, 10 ) == 'E75:Kiste:' ) {
		  $box = str_replace( '_', '', substr( $loc, 10 ));
		  break;
	  }
  }
  if( file_exists( $config['3djsondir']."/{$box}.json" )) {
	  $boxjson = file_get_contents( $config['3djsondir']."/{$box}.json" );
  }
?>
		<script>
			function initNEBIS() {
			  var renderer = $( '.renderer' );
			  renderer.height( '400px');
			  //var width = body.width();
			  ///renderer.width( width );
			  init3D( '<?php echo $box; ?>', '<?php echo $boxjson; ?>'  );

			}
		</script>
		
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
                    <?php echo htmlspecialchars( str_replace( '>>', '', str_replace( '<<', '', $entity->getTitle())) ); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $this->doc->id; ?>">
<?php
				$first = true;
				foreach( $authors as $author ) {
					echo ($first?"":"; ").htmlspecialchars( $author );
					$first = false;
				}
?>					
				
<?php			
				if( count( $authors ) > 1 ) echo "<br />\n";
				$publishers = $entity->getPublisher();
				$city = $entity->getCity();
				$year = $entity->getYear();
				if( $city ) echo htmlspecialchars( $city ).': '; 
				if( $publishers ) echo htmlspecialchars( implode( '/', $publishers )).', ';
				if( $year ) echo htmlspecialchars( $year ).'.';
				if( $city || $year || $publishers ) echo "<br />\n";

				$codes = $entity->getCodes();
				foreach( $codes as $code ) 
					echo htmlspecialchars( $code )."<br />\n";
				$signatures = $this->doc->signature;
				if( is_array( $signatures )) foreach( $signatures as $sig ) 
					if( substr( $sig, 0, 10 ) == 'nebis:E75:' ) echo 'Signatur: '.htmlspecialchars( substr( $sig, 10 ))."<br />\n";
				$locations = $entity->getLocations();
				foreach( $locations as $loc ) 
				if( substr( $loc, 0, 4 ) == 'E75:' ) echo 'Standort: Regal <b>'.$loc{10}.'</b> Kiste <b>'.htmlspecialchars( str_replace( '_', '', substr( $loc, 12 ))).' <a style="padding: 0px;" href="#" class="btn btn-default" data-toggle="modal" data-target="#MTModal" data-kiste="'.urlencode(str_replace( '_', '', substr( $loc, 10 ))).'" ><i class="fa fa-street-view" aria-hidden="true"></i></a></b><br />'."\n";
				$notes = $entity->getGeneralNote();
				foreach( $notes as $note ) 
					echo 'Anmerkung: '.htmlspecialchars( $note )."<br />\n";
				
				if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
					$us = explode( ':', $u );
					if( substr( $us[1], 0, 4 ) == 'http' ) {
						$url = substr( $u, strlen( $us[0])+1 );
						echo "{$us[0]}: <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i><a href=\"redir.php?id=".urlencode( $this->doc->id ).'&url='.urlencode( $url )."\" target=\"blank\">{$url}</a><br />\n";
					}
				}
				
				echo "ID: ".$this->doc->id."<br />\n";
				$inKiste = false;
				if( is_array( $this->doc->location )) foreach( $this->doc->location as $loc ) {
					if( substr( $loc, 0, strlen( 'E75:Kiste:' )) == 'E75:Kiste:' ) {
						$inKiste = true; 
						break;
					}
				}
				if( $inKiste ) { ?>
					<a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>"><i class="fa fa-folder-open" aria-hidden="true"></i> Details</a><br />
<?php			} ?>				
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