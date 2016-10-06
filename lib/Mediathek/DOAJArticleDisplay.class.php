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

class DOAJArticleDisplay extends DisplayEntity {
    private $metadata;
	private $entity;
	
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );
        
        $this->metadata = (array) json_decode( $this->data );
		$this->entity = new DOAJArticleEntity( $db );
		$this->entity->loadFromArray( $this->doc->originalid, $this->metadata, 'doajarticle');
    }

    public function getSchema() {
		$entity = $this->entity;
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = 'TechArticle';
		$schema['name'] = $this->doc->title;
		$schema['author'] = array();
		foreach( $entity->getAuthors() as $author ) {
			$schema['author'][] = array( '@type' => 'Person', 'name' => $author );
		}
		$schema['url'] = 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode( $this->doc->id );		
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );
		$schema['license'] = implode( '; ', $this->doc->license );

//		$schema['isPartOf'] = array();		
		$issn = array();
		foreach( $entity->getCodes() as $c )
			if( preg_match( '/^ISSN:/', $c ))
				$issn[] = substr( $c, 5 );
		$publishers = array();
		$ps = $entity->getPublisher();
		if( $ps && count( $ps ))
			foreach( $ps as $publisher ) {
				$publishers[] = array( '@type' => 'Organization', 'legalName' => $publisher );
			}
		foreach( $this->entity->getIssues() as $issue ) {
			if( preg_match( '/^(.*), ([^,]+), ([^,]+), ([^,]+)$/', $issue, $matches ))
			{
				$schema['isPartOf'] = array( "@type"=>"PublicationIssue",
					'issueNumber'=>$matches[3],
					'isPartOf'=>array( "@type"=>"PublicationVolume",
						'volumeNumber'=>$matches[2],
						'isPartOf'=>array( '@type'=>'Periodical',
							"name"=>$matches[1],
							"issn"=>$issn,
							'publisher'=>$publishers,
						)
					)
				);		
			}
		}		
		return $schema;
	}
    
	
	public function detailView() {
        global $config, $pagesize, $solrclient, $db, $urlparams;
		$html = '';
		$entity = $this->entity;
		$authors = $entity->getAuthors();
		$js_sourcelist = '';
		$ds = $config['defaultsource'];
		$ds[] = 'DOAJArticle';
		$ds = array_unique( $ds );
		foreach( $ds as $src )
			$js_sourcelist .= ",'".trim( $src )."'";
		$js_sourcelist = trim( $js_sourcelist, ',');
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
		<div class="row">
			<div class="col-md-3">
				<div style="">
				<span style="; font-weight: bold;">Aktueller Artikel</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
							<b><?php echo htmlspecialchars( str_replace( '>>', '', str_replace( '<<', '', strip_tags( $entity->getTitle())))); ?></b><br />
							<?php 
							$i = 0;
							foreach( $authors as $author ) { 
								if( $i > 0) echo "; "; ?>
									<a href="javascript:doSearchFull('author:&quot;<?php echo trim( $author ); ?>&quot;', '', [], {'source':[<?php echo $js_sourcelist; ?>]}, 0, <?php echo $pagesize; ?> );">
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
									<a href="javascript:doSearchFull('publisher:&quot;<?php echo trim( $publisher ); ?>&quot;', '', [], {'source':[<?php echo $js_sourcelist; ?>]}, 0, <?php echo $pagesize; ?> );">
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
										
										
							echo implode( '<br />', $this->doc->issue ).'<br />';
							$doajident = null;
							foreach( $this->doc->url as $url ) if( substr( $url, 0, 11 ) == 'identifier:' ) $doajident = substr( $url, 11 );
							if( $doajident ) {
								echo "<a href=\"redir.php?id=".urlencode( $this->doc->id ).'&url='.urlencode( $doajident )."\" target=\"_blank\">DOAJ Link</a><br />\n";
							}

?>							
					</div>
				</div>
			</div>
			<div class="col-md-6">
<?php
				$abstract = $entity->getAbstract();
				if( strlen( $abstract ) || true 	) {
?>
				<div style="">
				<span style="; font-weight: bold;">Abstract</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
						<?php echo $abstract."<p />&nbsp;<br />\n";
							echo "<b>Referenzen</b><br />\n";
							foreach( $this->doc->url as $url ) if( substr( $url, 0, 9 ) == 'relation:' ) {
								echo "<a style=\"word-break: break-all;\"href=\"redir.php?id=".urlencode( $this->doc->id ).'&url='.urlencode( substr( $url, 9 ))."\" target=\"_blank\">".htmlspecialchars( substr( $url, 9 ))."</a><br />\n";
							}

						?>
					</div>
				</div>
<?php
				}
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
?>
								<label>
									<a href="javascript:doSearchFull('', '', [], {source: [<?php echo $js_sourcelist; ?>], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
								</label><br />
<?php							
						}
?>							
					</div>
				</div>
						<?php  } ?>				
		</div>
		<div class="row">
			<div class="col-md-9">
				<div style="">
<?php
	$issues = array();
	foreach( $this->entity->getIssues() as $issue ) {
		if( preg_match( '/^(.*,) [^,]+$/', $issue, $matches ))
		{
			$issues[] = $matches[1];
		}
	}
?>
				<span style="; font-weight: bold;"><?php echo htmlspecialchars( implode( '; ', $issues )); ?></span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php

	$squery = $solrclient->createSelect();
	$helper = $squery->getHelper();
	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$phrase = '';
	$first = true;
	foreach( $issues as $issue ) {
		if( !$first ) $phrase .= ' AND ';
		$phrase .= 'issue:'.$helper->escapePhrase( $issue.'*' );
		$first = false;
	}

// Issue: 452º F : Revista de Teoría de la Literatura y Literatura Comparada, Iss 2, Pp 127-136 (2010)
	$qstr = $phrase;
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

			
		</div>
		<script>
			function initDOAJArticle() {


			}
		</script><?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
	}
	
    public function desktopList() {
		$html = '';
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( count( $this->entity->getAuthors() ) ? $this->entity->getAuthors()[0] : "" ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="fa fa-newspaper-o" aria-hidden="true"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( strip_tags( $this->doc->title )); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $this->doc->id; ?>">
					<?php $authors = $this->entity->getAuthors(); if( count( $authors ) > 1 ) for( $i = 1; $i < count( $authors ); $i++ ) echo htmlspecialchars( $authors[$i] ).($i < count( $authors )-1 ? " / " : ""); ?><br />
					<?php if ($this->highlight && count( $this->highlight )) { ?>
					<p style="background-color: #f0f0f0; margin-left: +20px; border-top: 1px solid black; border-bottom: 1px solid black;"><i>
					
<?php
						foreach ($this->highlight as $field => $highlight) {
							echo '(...) '.strip_tags( implode(' (...) ', $highlight), '<b></b>') . ' (...)' . '<br/>';
						}
?>
					</i></p>					
<?php
					}
?>
					<?php if( count( $this->entity->getIssues() )) echo "Issue: ".htmlentities( implode( '; ', $this->entity->getIssues()))."<br />\n"; ?>
					<?php if( count( $this->entity->getLanguages() )) echo "Language(s): ".htmlentities( implode( '; ', $this->entity->getLanguages()))."<br />\n"; ?>
					<?php if( count( $this->entity->getLanguages() )) echo "Publisher: ".htmlentities( implode( '; ', $this->entity->getPublisher()))."<br />\n"; ?>
					
					ID: <?php echo $this->doc->id; ?><br />
					<a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>"><i class="fa fa-folder-open" aria-hidden="true"></i> Details</a><br />
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