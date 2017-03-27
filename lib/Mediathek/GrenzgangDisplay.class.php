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

class GrenzgangDisplay extends DisplayEntity {

	var $entity;

    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->db = $db;
        $this->entity = new GrenzgangEntity( $db );
        $this->entity->loadFromDoc( $doc );
    }

    public function getSchema() {
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array('Book');
		$schema['@id'] = $this->doc->id;
		$schema['name'] = $this->doc->title;
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
		if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
			$us = explode( ':', $u );
			if( substr( $us[1], 0, 4 ) == 'http' ) {
				$url = substr( $u, strlen( $us[0])+1 );
				$schema['url'][]= $url;
			}
		}
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );

		$schema['license'] = implode( '; ', $this->doc->license );

		return $schema;
	}


  	public function getHeading() {
		$html = '';

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
		                    <h2 class="small-heading">Grenzgang</h2>

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
        $html .= ob_get_contents();
        ob_end_clean();
		return $html;
	}

	public function detailView() {

        global $config, $solrclient, $db, $urlparams, $pagesize, $session;
		$html = '';
		$data = (array)json_decode( $this->data, true );
		//var_dump( $data );
    $techmeta = null;
    if( array_key_exists( 'meta:tika', $data ))
		  $techmeta = $data['meta:tika'];

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
		<div class="row">
			<div class="col-md-3">
				<div style="">
				<span style="; font-weight: bold;">Aktuelles Element</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
						<h5><?php echo htmlspecialchars( $data['Titel'] ); ?></h5>
						<?php if( array_key_exists( 'Autor (Nachname, Vorname', $data )) { ?>
							<b>Autor(en): </b>
							<?php
							$authors = explode( ';', $data['Autor (Nachname, Vorname'] );
							$i = 0;
							foreach( $authors as $author ) {
								if( $i > 0) echo "; "; ?>
							<a href="javascript:doSearchFull('author:&quot;<?php echo str_replace('\'', '\\\'', trim( $author )); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
								<?php echo htmlspecialchars( $author ); ?>
							</a>
							<?php
						 		$i++;
							}
						?>
						<?php
							echo "<br />\n";
					 	} ?>
						<?php if( array_key_exists( 'Datum (JJJJ-MM-TT)', $data ) && strlen( trim( $data['Datum (JJJJ-MM-TT)']))) { ?>
						<b>Datum: </b><?php echo htmlspecialchars( $data['Datum (JJJJ-MM-TT)'] ); ?><br />
						<?php } ?>
						<?php if( array_key_exists( 'Ort / Strecke', $data ) && strlen( trim( $data['Ort / Strecke']))) { ?>
						<b>Ort / Strecke: </b><?php echo htmlspecialchars( $data['Ort / Strecke'] ); ?><br />
						<?php } ?>
						<?php if( array_key_exists( 'Beteiligt', $data )  && strlen( trim( $data['Beteiligt']))) { ?>
							<b>Beteiligt: </b>
							<?php
							if( $data['Beteiligt'] == "Team" ) echo "im Team";
							else {
								$authors = explode( ';', $data['Beteiligt'] );
								$i = 0;
								foreach( $authors as $author ) {
									if( $i > 0) echo "; "; ?>
								<!--
								<a href="javascript:doSearchFull('author:&quot;<?php echo str_replace('\'', '\\\'', trim( $author )); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
								-->
									<?php echo htmlspecialchars( $author ); ?>
								<!-- 	</a> -->
								<?php
							 		$i++;
								}
							}
						}
						?>


						<?php if( array_key_exists( 'Projektname', $data ) && strlen( trim( $data['Projektname']))) { ?>
						<b>Projekt: </b><?php echo htmlspecialchars( $data['Projektname'] ); ?><br />
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-md-6">
<?php

	switch( $this->entity->getType() ) {
		case 'sound':
			include( 'grenzgang/sound_detail.inc.php' );
			break;
		case 'ressource':
			include( 'grenzgang/ressource_detail.inc.php' );
			break;
		case '2d':
			include( 'grenzgang/2d_detail.inc.php' );
			break;
		case 'projectable':
			include( 'grenzgang/projectable_detail.inc.php' );
			break;
		case 'track':
			include( 'grenzgang/track_detail.inc.php' );
			break;
    case 'folder':
			include( 'grenzgang/folder_detail.inc.php' );
			break;
		default:
			include( 'grenzgang/default_detail.inc.php' );
			break;

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
//						foreach( $entity->getCluster() as $cl ) {
//							echo htmlspecialchars( $cl ).'<br />';
?>
								<label>
									<a href="javascript:doSearchFull('', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
								</label><br />

<?php
						}
?>
					</div>
				</div>
		<?php  } ?>
			</div>
		</div>
	<div class="row">
		<div class="col-md-12">
<?php
$locations = array();
foreach( $this->doc->location as $loc ) {
  if( !preg_match( '/^parent:/', $loc )) continue;
  $locations[] = $loc;
}
if( count( $locations ) || $this->entity->getType() == 'folder' ) {
?>
				<div style="">
				<span style="; font-weight: bold;">Weitere Ressourcen</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
	$squery = $solrclient->createSelect();
	$helper = $squery->getHelper();

	$acl_query = '';
	foreach( $session->getGroups() as $grp ) {
		if( strlen( $acl_query ) > 0 ) $acl_query .= ' OR';
		$acl_query .= ' acl_meta:'.$helper->escapePhrase($grp);
	}
	$squery->createFilterQuery('acl_meta')->setQuery($acl_query);
	echo "<!-- filter acl_meta: {$acl_query} -->\n";


	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$qstr = '';
  if( $this->entity->getType() == 'folder' ) {
      $qstr = 'location:'.$helper->escapePhrase( 'parent:'.$this->doc->id );
  }
  else {
  	foreach( $locations as $loc ) {
  		if( strlen( $qstr )) $qstr .= ' OR ';
  		$qstr = 'location:'.$helper->escapePhrase( $loc );
  		if( preg_match( '/^parent:(.*)$/', $loc, $matches ))
  			$qstr .= ' OR id:'.$helper->escapePhrase( $matches[1] );
  	}
  	$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
  }
	echo "\n<!-- {$qstr} -->\n";
	$squery->setQuery( $qstr );
	$squery->addSort('id', $squery::SORT_ASC);
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
<?php } ?>
<?php
if( DEBUG ) {
	?>
				<div style="">
				<span style="; font-weight: bold;">RAW Data</span><br />
					<div class="facet" style="padding: 0px;">
						<div class="marker" style=""></div>
						<div>
							<pre>
<?php
	print_r( $data );
?>
							</pre>
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
<?php
	}
?>

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
        $data = (array)json_decode( $this->data, true );

		$entity = new GrenzgangEntity( $this->db );
		$entity->loadFromDoc( $this->doc );

		$collid = preg_replace( '/[^a-zA-Z0-9]/', '_', $this->doc->id );

		$t = strtolower( $this->doc->type );
		$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $collid; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $collid; ?>">
					<?php echo htmlspecialchars( implode( '; ', $entity->getAuthors())  ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="<?php echo $icon; ?>"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $collid; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $collid; ?>">
                    <?php echo htmlspecialchars( $entity->getTitle() ); ?>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $collid; ?>">

<?php

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
				<?php if( array_key_exists( 'Datum (JJJJ-MM-TT)', $data ) && strlen( trim( $data['Datum (JJJJ-MM-TT)']))) { ?>
				<b>Datum: </b><?php echo htmlspecialchars( $data['Datum (JJJJ-MM-TT)'] ); ?><br />
				<?php } ?>
        <?php

        if( @isset( $data['meta:thumb'] )) echo "<object type=\"image/jpeg\" data=\"".(is_array( $data['meta:thumb'] ) ? $data['meta:thumb'][0] : $data['meta:thumb'])."\"></object><br />\n";

				echo "ID: ".$this->doc->id."<br />\n";
?>
					<a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>">Detail</a><br />

                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
