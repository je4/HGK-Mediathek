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

class swissbibDisplay extends DisplayEntity {
    
	var $entity;
	
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->db = $db;
		$this->entity = new swissbibEntity( $db );
		$record = new OAIPMHRecord( $this->data );
		$this->entity->loadNode( $doc->originalid, $record, 'swissbib' );
    }

    public function getSchema() {
		$entity = $this->entity;
		$schema = array();
		$schema['@context'] = 'http://schema.org';
//		$schema['@id'] =  '#record';
		$schema['@id'] = $this->doc->id;
		if( $this->isJournal( $entity )) {
			$schema['@type'] = array( 'Periodical' );
		foreach( $this->doc->code as $c )
				if( preg_match( '/^ISSN:/', $c ))
					$schema['issn'] = substr( $c, 5 );
		}
		else {
			$schema['@type'] = array( 'Book' );
			foreach( $entity->getCodes() as $c )
				if( preg_match( '/^ISBN:/', $c ))
					$schema['isbn'] = substr( $c, 5 );
		}
		$schema['name'] = $this->doc->title;
		$schema['author'] = array();
		foreach( $entity->getAuthors() as $author ) {
			$schema['author'][] = array( '@type' => 'Person', 'name' => $author );
		}
		$publishers = $entity->getPublisher();
		if( $publishers && count( $publishers ))
			foreach( $publishers as $publisher ) {
				$schema['publisher'][] = array( '@type' => 'Organization', 'legalName' => $publisher );
			}
		$schema['url'] = array( 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode( $this->doc->id ));	
		if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
			$us = explode( ':', $u );
			if( substr( $us[1], 0, 4 ) == 'http' ) {
				$url = substr( $u, strlen( $us[0])+1 );
				$schema['url'][] = $url;
			}
		}
		
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );
		$schema['license'] = implode( '; ', $this->doc->license );
		return $schema;
	}
	
	public function isJournal() {
		foreach( $this->entity->getCodes() as $c ) {
			if( preg_match( '/^ISSN:/', $c ))
				return true;
		}
		return false;
	}
	
	public function detailView() {
        global $config, $googleservice, $googleclient, $solrclient, $db, $urlparams, $pagesize;
        $html = '';

        $entity = $this->entity;
        
        if( DEBUG ) {
//        	ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
        	$solr = new SOLR( $solrclient );
        	$solr->import( $entity, true );
//        	ob_end_clean();
        }

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
        
		$authors = array_unique( $entity->getAuthors());
		$js_sourcelist = '';
		$ds = $config['defaultsource'];
		$ds[] = 'swissbib';
		$ds = array_unique( $ds );
		foreach( $ds as $src )
			$js_sourcelist .= ",'".trim( $src )."'";
		$js_sourcelist = trim( $js_sourcelist, ',');

		$squery = $solrclient->createSelect();
		$helper = $squery->getHelper();

		
		$kiste = array();
		if( is_array( $this->doc->location )) foreach( $this->doc->location as $loc ) {
		  if( substr( $loc, 0, 10 ) == 'E75:Kiste:' ) {
			  $kiste[] = substr( $loc, 10 );
		  }
		} 

        
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
									<a href="javascript:doSearchFull('author:&quot;<?php echo str_replace('\'', '\\\'', trim( $author )); ?>&quot;', '', [], {'source':[<?php echo $js_sourcelist; ?>]}, 0, <?php echo $pagesize; ?> );">
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
									<a href="javascript:doSearchFull('publisher:&quot;<?php echo str_replace('\'', '\\\'', trim( $publisher )); ?>&quot;', '', [], {'source':[<?php echo $js_sourcelist; ?>]}, 0, <?php echo $pagesize; ?> );">
										<?php echo htmlspecialchars( $publisher ); ?>
									</a>
							<?php 
								}
							}
								
							if( $year ) echo ', '.htmlspecialchars( $year );
							if( $city || $year || $publishers ) echo "<br />\n";

							foreach( $entity->getPhysicalDescription() as $descr ) {
								echo htmlspecialchars( $descr )."<br />\n";
							}
							foreach( $entity->getIssues() as $issue ) {
								echo htmlspecialchars( $issue )."<br />\n";
							}

							$codes = $entity->getCodes();
							if( is_array( $codes )) foreach( $codes as $code ) {
								echo htmlspecialchars( str_replace( ':', ': ', $code ))."<br />\n";
							}

							$rib = null;
							/*
							if( isset( $config['RIB'] )) {
								$rib = new RIB( $config['RIB'] );
								$rib->load( $this->doc->originalid );
							}
							else
							{
								echo "<!-- error: no rib -->";
							}
							*/
?>							<div id='RIB'></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div style="">
				<span style="; font-weight: bold;">MARC</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div>
<table class="table table-striped">
	<tbody>
<?php
	$data = $this->entity->getData();
	foreach( $data as $field=>$val ) {
		foreach( $val as $ind1=>$val2 ) {
			foreach( $val2 as $ind2=>$val3 ) {
				foreach( $val3 as $val4 ) {
					echo "<tr>\n";
					echo "  <td style=\"padding:.25rem;\">".$field.str_replace( ' ', '&nbsp;', $ind1.$ind2 )."</td>\n";
					echo "  <td style=\"padding:.25rem;\">";
					foreach( $val4 as $sub=>$val5 ) {
						foreach( $val5 as $val6 ) {
							echo "|{$sub} ".htmlspecialchars( $val6 );
						}
					}
					echo "  </td>\n";
					echo "</tr>\n";
				}
			}
		}
	}
?>	
	</tbody>
</table>						
						</div>
					</div>
				</div>
				
				<div style="">
				<span style="; font-weight: bold;">Document</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div>
							<pre>
<?php 
	var_dump( $this->doc->getFields());
?>						
							</pre>
						</div>
					</div>
				</div>				
				
				<div style="">
				<span style="; font-weight: bold;">XML</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div>
<?php
		// Tidy
		$cfg = array(
				'indent'         => true,
				'output-xml'   => true,
				'input-xml' => true,
				'wrap'           => 150);
		$tidy = new \Tidy;
		$tidy->parseString($this->entity->getXML(), $cfg, 'utf8');
		$tidy->cleanRepair();
        echo '<pre>'.( htmlspecialchars( $tidy )).'</pre>';

?>
						</div>
					</div>
				</div>				
		
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
									<a href="javascript:doSearchFull('', '', [], {'source':[<?php echo $js_sourcelist; ?>], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
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
if( count( $kiste )) {
?>
		<div class="row">
			<div class="col-md-9">
				<div style="">
				<span style="; font-weight: bold;">Weitere Bücher in Kiste <?php echo implode( ' / ',$kiste ); ?></span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php						
	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$qstr = '';
	foreach( $kiste as $k ) {
		if( strlen( $qstr )) $qstr .= ' OR ';
		$qstr .= 'location:'.$helper->escapePhrase( 'E75:Kiste:'.$k );
	}
	$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
	//echo "\n<!-- {$qstr} -->\n";
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
			function initswissbib() {

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
        		
		$entity = $this->entity;

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
				if ($this->highlight && count( $this->highlight )) { 
?>
				<p style="background-color: #f0f0f0; margin-left: +20px; border-top: 1px solid black; border-bottom: 1px solid black;"><i>
<?php
					foreach ($this->highlight as $field => $highlight) {
						echo '(...) '.strip_tags( implode(' (...) ', $highlight), '<b></b>') . ' (...)' . '<br/>';
					}
				}
?>
				</i></p>
<?php 									
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
?>
				<a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>"><i class="fa fa-folder-open" aria-hidden="true"></i> Details</a><br />
				<p>
				</p>
					<!-- <a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>">Detail</a><br /> -->
                    <!-- <pre><?php /* echo ( htmlspecialchars( $tidy ));  */ ?></pre> -->
                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}