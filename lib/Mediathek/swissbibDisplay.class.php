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

    public function __construct( $doc, $urlparams, $db, $highlightedDoc, $bypass = false ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );
			if( !$bypass ) {
		        $this->db = $db;
				$this->entity = new swissbibEntity( $db );
				$record = new OAIPMHRecord( $this->data );
				$this->entity->loadNode( $doc->originalid, $record, 'swissbib' );
			}
    }

    public function getSchema() {
		$entity = $this->entity;
		$schema = array();
		$schema['@context'] = 'http://schema.org';
//		$schema['@id'] =  '#record';
		$schema['@id'] = $this->doc->id;
		if( $this->isJournal( $entity )) {
			$schema['@type'] = array( 'Periodical' );
		if( @isset( $this->doc->code )) foreach( $this->doc->code as $c )
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
        global $config, $googleservice, $googleclient, $solrclient, $db, $urlparams, $pagesize, $session;
        $html = '';

        $entity = $this->entity;


        if( DEBUG && $session->isLoggedIn()) {
        	//$solr = new SOLR( $solrclient, $this->db );
        	//$solr->import( $entity, true );
        }

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);

		$authors = array_unique( $entity->getAuthors());

		$squery = $solrclient->createSelect();
		$helper = $squery->getHelper();

		$kisten = array();
		if( @is_array( $this->doc->location )) foreach( $this->doc->location as $loc ) {
			if( strncmp( 'NEBIS:E75:', $loc, 10) == 0 ) {
				$k = substr( $loc, 10 );
				if( preg_match( '/^[A-Za-z]_[0-9]{3}_[ab]$/', $k )) $kisten[] = $k;
			}
		}
		$abstract = trim( $this->entity->getAbstract());
		$urls = $this->entity->getURLs();
		$urls_notes = $this->entity->getURLsNotes();

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
									<a href="javascript:doSearchFull('author:&quot;<?php echo str_replace('\'', '\\\'', trim( $author )); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
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
									<a href="javascript:doSearchFull('publisher:&quot;<?php echo str_replace('\'', '\\\'', trim( $publisher )); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
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
							$nebisid = null;
							if( @is_array( $this->doc->sourceid )) foreach( $this->doc->sourceid as $sourceid ) {
								if( strncmp( $sourceid, '(NEBIS)', 7 ) == 0 ) $nebisid = substr( $sourceid, 7 );
							}
							if( isset( $config['RIB'] )) {
								if( strlen( $nebisid )) {
									$rib = new RIB( $config['RIB'] );
									$rib->load( $nebisid );
								}
							}
							else
							{
								echo "<!-- error: no rib -->";
							}
							$signatures = $this->doc->signature;
							if( is_array( $signatures )) foreach( $signatures as $sig ) {
								foreach( $config['swissbibsig'] as $prefix ) {
									if( strncmp( $sig, $prefix, strlen( $prefix) ) == 0 ) {
										$signature = substr( $sig, strlen( $prefix ));
										echo 'Signatur: <a href="'.'https://recherche.nebis.ch/primo-explore/fulldisplay?docid=ebi01_prod'.urlencode( $nebisid.'&context=L&vid=NEBIS&lang=de_DE&search_scope=default_scope&adaptor=Local%20Search%20Engine&tab=default_tab' )
										.'" target="_blank">'.htmlspecialchars( $signature )."</a><br />\n";
										$availability = $rib ? $rib->getAvailability($signature) : null;
										if( $availability ) {
											echo "<span style=\"font-size: 80%; line-height: 80%\">&nbsp;&nbsp; Ausleihstatus: {$availability}<br />\n";
											echo "&nbsp;&nbsp; Benutzung: ".$rib->getStatus()	."<br /></span>\n";
										}
									}
								}
							}
							$thumb = $rib ? $rib->getThumb() : null;
							if( $thumb ) {
								echo "<img src=\"{$thumb}\" /><br />\n";
							}
/*
							foreach( $this->entity->getBibs() as $bib ) {
								echo htmlspecialchars( $bib )."<br />\n";
							}
*/
?>
								Quelle: <a href="https://www.swissbib.ch/Record/<?php  echo $this->doc->originalid; ?>">swissbib</a></a><br />
								ID: <?php  echo $this->doc->id; ?><br />
							<div id='RIB'></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
<?php
	if( count( $kisten ) > 0 ) {
?>
				<div style="">
				<span style="; font-weight: bold;">Raumübersicht</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div class="renderer2"></div>
					</div>
				</div>
<?php
	}
?>
<?php
	if( strlen( $abstract ) > 1 || count( $urls ) ) {
?>
				<div style="">
				<span style="; font-weight: bold;">Abstract & Referenzen</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<?php if( strlen( $abstract )) echo "<p>".nl2br( htmlspecialchars( $abstract ))."</p>\n"; ?>
<?php
if( @is_array( $urls_notes )) foreach( $urls_notes as $url ) {
		$text = ( strlen( $url['note'] ) ? $url['note'] :(strlen( $url['url'] ) > 60 ? substr( $url['url'], 0, 60 ).'...' : $url['url'] ) );
		echo "<i class=\"fa fa-external-link\" aria-hidden=\"true\"></i><a href=\"".$url['url']."\" target=\"blank\">".htmlspecialchars( $text )."</a><br />\n";
}
?>
					</div>
				</div>
<?php
	}
?>
<?php
// BEGIN Bereich kurz
if( true ) {

	$cquery = $solrclient->createSelect();
	$chelper = $cquery->getHelper();
	$cquery->setRows( 0 );
	$filter = '';
	$area = '';
/*
	$cats = array();
	$fields = array();
	foreach( $this->doc->category as $key=>$cat ) {
		if( preg_match( '/^[1-3]!!(area|field)!!(.+)$/i', $cat, $matches )) {
			if( strpos( 'Regal', $cat  ) !== false ) continue;
			$fields[] = $matches[1].'!!'.$matches[2];
		}
	}
	sort( $fields );
	$fields = array_reverse( $fields );
	$fields2 = [];
	$last = 'x';
	foreach( $fields as $fld ) {
		if( substr( $last, 0, strlen( $fld )) == $fld ) continue;
		$fields2[] = $fld;
		$last = $fld;
	}


	//print_r( $fields );
	//print_r( $fields2 );
	foreach( $fields2 as $fld ) {
		$num = count( explode( '!!', $fld ))-1;
		$filter .= (strlen( $filter ) == 0 ? '' : ' OR ').'category:'.$helper->escapePhrase($num.'!!'.$fld);
	}
*/
	foreach( $this->doc->category as $key=>$cat ) {
		if( preg_match( '/^1!!area!!([^!]+)$/', $cat, $matches ) ) {
			$filter = 'category:'.$helper->escapePhrase($cat);
			$alabel = $matches[1];
			$area = $cat;
		}
	}

	if( strlen( $filter )) {
		echo "<!-- filter: {$filter} -->\n";
		$facetSetArea = $cquery->getFacetSet();
		$facetSetArea->createFacetField('institute')->setField('category')->setPrefix( '1!!institute' )->setLimit( 10 );

		$cquery->createFilterQuery('category')->addTag('category')->setQuery( $filter );
		$cquery->setQuery( '*:*' );
		$rs = $solrclient->select( $cquery );
		$facetInstitute = $rs->getFacetSet()->getFacet('institute');
//		die( "end" );

?>
				<div style="">
				<span style="; font-weight: bold;">Verwandte Bereiche</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
								<label>
<?php
								$first = true;
								foreach( $this->doc->category as $key=>$cat ) {
									if( preg_match( '/^1!!field!!(.+)$/', $cat, $matches )) {
?>
										<?php echo $first ? '': ' // '; ?><span style="white-space: nowrap;"><a href="javascript:doSearchFull('category:<?php echo htmlspecialchars( $chelper->escapePhrase( $cat )); ?>', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
											<?php echo htmlspecialchars( trim( "{$matches[1]}" )); ?>
										</a></span>
<?php
										$first = false;
									}
								}
								echo ($first ? '' : '<br />' )."Weitere {$alabel} in den Bereichen<br />\n";
								$first = true;
								foreach ($facetInstitute as $value => $count) {
									//echo "{$value}:{$count} ";

									if( $count > 0 && preg_match( '/^1!!institute!!(.*)/i', $value, $matches )) {
?>
									<?php echo $first ? '': ' // '; ?><span style="white-space: nowrap;"><a href="javascript:doSearchFull('category:<?php echo htmlspecialchars( $chelper->escapePhrase( $value )); ?> category:<?php echo htmlspecialchars( $chelper->escapePhrase( $area )); ?>', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
										<?php echo htmlspecialchars( trim( "{$matches[1]} ({$count})" )); ?>
									</a></span>
<?php
									$first = false;
								}
							}
?>
							</label>
					</div>
				</div>
<?php
	}
}
// END Bereich kurz
?>

<?php
// BEGIN Bereich
if( DEBUG ) {

	$cquery = $solrclient->createSelect();
	$chelper = $cquery->getHelper();
	$cquery->setRows( 500 );
	$filter = '';

	$cats = array();
	foreach( $this->entity->getCategories() as $key=>$cat ) {
		echo "<!-- cat: {$cat} -->\n";
		if( preg_match( '/^area!!([^!]+)$/i', $cat, $matches )) {
			$filter .= (strlen( $filter ) == 0 ? '' : ' OR ').'category: '.$helper->escapePhrase('1!!'.$cat);
		}
	}
	if( strlen( $filter )) {
		echo "<!-- filter: {$filter} -->\n";
		$facetSetArea = $cquery->getFacetSet();
		$facetSetArea->createFacetField('area')->setField('category')->setPrefix( '2!!area' );

		$cquery->createFilterQuery('area')->addTag('area')->setQuery( $filter );
		$cquery->setQuery( '*:*' );
		$rs = $solrclient->select( $cquery );
?>
				<div style="">
				<span style="; font-weight: bold;">Bereich</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
								$facetArea = $rs->getFacetSet()->getFacet('area');
								$bereich = array();
								foreach ($facetArea as $value => $count) {
									if( $count > 0 && preg_match( '/^2!!area!!(.*)!!(.*)/i', $value, $matches )) {
										if( !@is_array( $bereich[$matches[1]])) $bereich[$matches[1]] = array( 'name'=>$matches[1]
																								, 'count'=>0
																								, 'category'=>"1!!area!!{$matches[1]}"
																								, 'location'=>array());
										$bereich[$matches[1]]['location'][] = array( 'name'=>$matches[2], 'category'=>$value, 'count'=>$count );
										$bereich[$matches[1]]['count'] += $count;
									}
								}
								foreach( $bereich as $b ) {
									if( $b['count'] > 0 ) {
?>
								<label style="text-indent: -40px; margin-left: 40px;">
									<a href="javascript:doSearchFull('', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>], 'area':[<?php echo htmlspecialchars( $chelper->escapePhrase( $b['category'] )); ?>]}, 0, <?php echo $pagesize; ?> );">
										<span style="color: black;"><?php echo htmlspecialchars( "{$b['name']} ({$b['count']})" ); ?></span>
									</a>
<?php
											foreach( $b['location'] as $l ) {
?>
												- <a href="javascript:doSearchFull('category:<?php echo htmlspecialchars( $chelper->escapePhrase( $l['category'] )); ?>', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
													<span style="white-space: nowrap;"><?php echo htmlspecialchars( "{$l['name']} ({$l['count']})" ); ?></span>
												</a>
<?php
											}
?>
								</label><br />
<?php
								}
						}
?>
					</div>
				</div>
<?php
	}
}
// END Bereich
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
									<a href="javascript:doSearchFull('', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
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

		<?php
		// BEGIN Places
		if( true ) {

			$theCats = array_unique( $this->doc->category );
			foreach( $theCats as $cat ) {
				if( preg_match( '/^1!!field!!([^!]*)$/i', $cat, $matches )) {
					echo "<!-- Places: $cat -->\n";
				$cquery = $solrclient->createSelect();
				$chelper = $cquery->getHelper();
				$cquery->setRows( 0 );
				$filter = 'category:'.$chelper->escapePhrase( $cat );

				echo "<!-- filter: {$filter} -->\n";
				$facetSetArea = $cquery->getFacetSet();
				$facetSetArea->createFacetField('area')->setField('category')->setPrefix( ( '2!!'.substr( $cat, 3 ).'!!'));

				$cquery->createFilterQuery('area')->addTag('area')->setQuery( $filter );
				$cquery->setQuery( '*:*' );
				$rs = $solrclient->select( $cquery );

		?>
						<div style="">
						<span style="; font-weight: bold;">Bereich <?php echo htmlspecialchars( $matches[1] ); ?></span><br />
							<div class="facet" style="">
								<div class="marker" style=""></div>
		<?php
										$facetArea = $rs->getFacetSet()->getFacet('area');
										foreach ($facetArea as $value => $count) {
											echo "<!-- facet: {$value} ({$count}) -->\n";
											if( $count > 0 && preg_match( '/^2!!'.preg_quote( substr( $cat, 3 )).'!!(Regal.*)/i', $value, $matches2 )) {
		?>
										<label>
											<a href="javascript:doSearchFull('category:<?php echo htmlspecialchars( $chelper->escapePhrase( $value )); ?>', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
												<?php echo htmlspecialchars( "{$matches2[1]} ({$count})" ); ?>
											</a>
										</label><br />
		<?php
											}
										}
		?>
							</div>
						</div>
		<?php
						}
					}
		}
		// END Places
		?>
	</div>

	</div>
	<div class="row">
		<div class="col-md-12">
<?php
if( count( $kisten )) {
?>
				<div style="">
				<span style="; font-weight: bold;">Weitere Bücher in Kiste <?php echo implode( ' / ',$kisten ); ?></span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$qstr = '';
	foreach( $kisten as $k ) {
		if( strlen( $qstr )) $qstr .= ' OR ';
		$qstr .= 'location:'.$helper->escapePhrase( 'NEBIS:E75:'.$k );
	}
	$cats = array();
	foreach( $this->doc->category as $cat ) {
		echo "<!-- cat: {$cat} -->\n";
		if( preg_match( '/^1!!area!!([^!]+)/i', $cat, $matches )) {
			if( $matches[1] == 'unknown' ) continue;
			$cats[] = 'category:'.$helper->escapePhrase( '2!!'.substr( $cat, 3 ).'!!Online');
		}
	}
	if( count( $cats )) $qstr = "({$qstr}) OR (".implode( ' OR ', $cats ).")";

	$isbns = array();
	if( is_array( $this->doc->code )) foreach( $this->doc->code as $code ) {
		echo "<!-- code: {$code} -->\n";
		if( preg_match( '/^E?ISBN:(.+)/i', $code, $matches )) {
			$isbns[] = 'code:'.$helper->escapeTerm($code);
		}
	}
	if( count( $isbns )) $qstr = "({$qstr}) OR (".implode( ' OR ', $isbns ).")";
	$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
	echo "\n<!-- {$qstr} -->\n";
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
<?php
}
else {
	$fields = [];
	$labels = [];
	foreach( $this->doc->category as $key=>$cat ) {
//		echo "{$cat} - \n";
		if( preg_match( '/^(1!!field|2!!area)!!(.*)$/', $cat, $matches )) {
			$fields[] = $cat;
			$labels[] = str_replace( '!!', ':', $matches[2] );
		}
	}
	if( count( $fields )) {
	?>
					<div style="">
					<span style="; font-weight: bold;">Weitere Bücher in den Feldern <?php echo implode( ' // ',$labels ); ?></span><br />
						<div class="facet" style="">
							<div class="marker" style=""></div>
	<?php
		$squery->setRows( 100 );
		$squery->setStart( 0 );
		$qstr = '';
		foreach( $fields as $f ) {
			if( strlen( $qstr )) $qstr .= ' OR ';
			$qstr .= 'category:'.$helper->escapePhrase( $f );
		}

		$isbns = array();
		if( is_array( $this->doc->code )) foreach( $this->doc->code as $code ) {
			echo "<!-- code: {$code} -->\n";
			if( preg_match( '/^E?ISBN:(.+)/i', $code, $matches )) {
				$isbns[] = 'code:'.$helper->escapeTerm($code);
			}
		}
		if( count( $isbns )) $qstr = "({$qstr}) OR (".implode( ' OR ', $isbns ).")";
		$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
		echo "\n<!-- {$qstr} -->\n";
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
	<?php
	}
}
 ?>

<?php
if( DEBUG ) {
	?>
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
				<span style="; font-weight: bold;">Data</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div>
							<pre>
<?php
	print_r( $this->entity->getData());
?>
							</pre>
						</div>
					</div>
				</div>

<?php
	}
?>
			</div>
<!--
			<div class="col-md-3">
			</div>
 -->
		</div>
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
  if( count( $kisten ) > 0 ) {
  	$box = str_replace( '_', '', $kisten[0] );
  }
  if( file_exists( $config['3djsondir']."/{$box}.json" )) {
	  $boxjson = file_get_contents( $config['3djsondir']."/{$box}.json" );
  }
?>
		<script>
			function initswissbib() {
				signatures = [<?php if( isset( $this->doc->signature )) foreach( $this->doc->signature as $sig ) if( substr( $sig, 0, 10 ) == 'nebis:E75:' ) 	echo '"'.substr( $sig, 10 ).'", '; ?>];
				$.post( 'RIB.php', { sys: <?php echo '"'.$this->doc->originalid.'"'; ?>, sig: signatures }).done( function( data ) {
					$('#RIB' ).html( data );
				});

<?php
	if( count( $kisten ) > 0 ) {
		$boxlist = '';
		foreach( $kisten as $k ) {
			if( strlen( $boxlist )) $boxlist .= ', ';
			$boxlist .= "'".str_replace( '_', '', $k )."'";
		}
?>
				var renderer = $( '.renderer2' );
				renderer.height( '400px');
				//var width = body.width();
				///renderer.width( width );
				init3D( [<?php echo $boxlist; ?>], '<?php echo $boxjson; ?>', '.renderer2'  );
<?php } ?>
			}
		</script>

<?php
        $html .= ob_get_contents();
        ob_end_clean();

				addJS( array(
					"js/threejs/build/three.js",
					"js/threejs/build/TrackballControls.js",
					"js/threejs/build/OrbitControls.js",
					"js/threejs/build/CombinedCamera.js",
					"js/mediathek.js",
					"js/mediathek3d.js",
					"js/threex.domresize.js",
				));


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

        $t = strtolower( $this->doc->type );
        $icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];
        if( @is_array( $this->doc->catalog )) {
        	if( array_search( 'NATIONALLICENCE', $this->doc->catalog ) !== false ) {
        		$icon = $config['icon']['nationallizenz'];
					}
				}
				if( $this->doc->openaccess == true ) {
					$icon = $config['icon']['eopenaccess'];
				}

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
					<?php if( count( $authors )) echo htmlspecialchars( $authors[0]  );
					 if( count( $authors ) > 1) echo " et al."; ?>
                </a>
            </td>
            <td class="list" style="width: 5%; font-size: 20px; white-space: nowrap;">
							<i class="<?php echo $icon; ?>"></i><?php if( $this->doc->openaccess && false ) { ?><img src="Open_Access_logo_PLoS_transparent.svg" style="width:12px;"><?php } ?>
						</td>
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
				if( count( $authors ) >= 1 ) echo "<br />\n";
				if ($this->highlight && count( $this->highlight )) {
?>
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
				if( is_array( $signatures )) foreach( $signatures as $sig ) {
					foreach( $config['swissbibsig'] as $prefix ) {
						if( strncmp( $sig, $prefix, strlen( $prefix) ) == 0 ) echo 'Signatur: '.htmlspecialchars( substr( $sig, strlen( $prefix ) ))."<br />\n";
					}
				}
				$locations = $entity->getLocations();
				foreach( $locations as $loc ) {
					if( strncmp( 'NEBIS:E75:', $loc, 10 ) == 0 ) {
						$k = substr( $loc, 10 );
						if( preg_match( '/^[A-Za-z]_[0-9]{3}_[ab]$/', $k )) {
								$box = urlencode(str_replace( '_', '', $k ));
								$boxjson = null;
								if( file_exists( $config['3djsondir']."/{$box}.json" )) {
									$boxjson = file_get_contents( $config['3djsondir']."/{$box}.json" );
								}
								echo 'Standort: Regal <b>'.$loc{10}.'</b> Kiste <b>'.htmlspecialchars( str_replace( '_', '', substr( $loc, 12 )))
									.' <a style="padding: 0px;" href="#" class="btn btn-default" data-toggle="modal" data-target="#MTModal" data-kiste="'.$box.'" data-json="'.htmlspecialchars( $boxjson, ENT_QUOTES ).'" ><i class="fa fa-street-view" aria-hidden="true"></i></a></b><br />'."\n";
							}
							elseif( substr( $loc, 10 ) == 'Archiv') {
								echo 'Standort: <b>Archiv</b><br />'."\n";
							}
						}
    			}
				$notes = $entity->getGeneralNote();
				foreach( $notes as $note )
					echo 'Anmerkung: '.htmlspecialchars( $note )."<br />\n";

				$urls_notes = $this->entity->getURLsNotes();
				if( @is_array( $urls_notes )) foreach( $urls_notes as $url ) {
					$text = ( strlen( $url['note'] ) ? $url['note'] :(strlen( $url['url'] ) > 60 ? substr( $url['url'], 0, 60 ).'...' : $url['url'] ) );
					echo "<i class=\"fa fa-external-link\" aria-hidden=\"true\"></i><a href=\"".$url['url']."\" target=\"blank\">".htmlspecialchars( $text )."</a><br />\n";
				}

?>
				Quelle: <a href="https://www.swissbib.ch/Record/<?php  echo $this->doc->originalid; ?>">swissbib</a></a><br />
<?php
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
