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

class HGKWebDisplay extends DisplayEntity {
	private $metadata;
  private $entity;

    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->entity = new WebEntity( $this->db );
        $this->entity->loadFromDoc( $this->doc );

		    $this->metadata = $this->entity->getData();
    }

	public function getSchema() {
		global $config;

		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array('Movie');
		$schema['@id'] = $this->doc->id;
		$schema['name'] = $this->doc->title;
		$schema['author'] = array();
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
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );

		$schema['license'] = implode( '; ', $this->doc->license );

		return $schema;
	}

	public function getHeading() {
	$html = '';

			ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
				<h2 class="small-heading">Archiv<br />Institut Industrial Design</h2>

				<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
			$html .= ob_get_contents();
			ob_end_clean();
	return $html;
}

	public function detailView() {
      global $config, $googleservice, $googleclient, $solrclient, $db, $urlparams, $pagesize, $session;

		$intern = $session->inGroup( 'location/fhnw');
		$loggedin = $session->isLoggedIn();

		$authors = $this->entity->getAuthors();
		$squery = $solrclient->createSelect();
		$helper = $squery->getHelper();

		$html = '';

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
<div class="row">
	<div class="col-md-3">
		<span style="; font-weight: bold;">Aktuelles Objekt</span><br>
		<div class="facet" style="">
			<div class="marker" style=""></div>
			<h5><?php
      $first = true;
        foreach( $this->metadata['Benutzer'] as $benutzer ) {
          echo ($first?"":"; ");
					$author = $benutzer['Nachname'].', '.$benutzer['Vorname'];
					?>
					<a href="javascript:doSearchFull('author:&quot;<?php echo htmlspecialchars( $author ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
						<?php echo htmlspecialchars( $author ); ?>
					</a>
					<?php
						echo '('.$benutzer['Rolle'].')';
          $first = false;
        }
        echo ': '.htmlentities( $this->metadata['Titel'] );?>
        <span style="font-size: 80%;"><?php echo htmlspecialchars( $this->entity->getYear() );?></span>
      </h5>
			<?php echo $this->metadata['Kurzbeschreibung']; ?><br />
			Modul:
			<a href="javascript:doSearchFull('issue:&quot;<?php echo $this->metadata['Modul']['Kennziffer'].' - '.$this->metadata['Modul']['Titel']; ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
				<?php echo $this->metadata['Modul']['Kennziffer'].' - '.$this->metadata['Modul']['Titel']; ?>
			</a><br />
      <span style="font-size: 80%;">
			<?php if( strlen( $this->metadata['Zusammenarbeit'])) { ?>
				<b>Zusammenarbeit: </b><?php echo ( $this->metadata['Zusammenarbeit']); ?><br />
			<?php }	?>
			<?php if( strlen( $this->metadata['Recherche'])) { ?>
				<b>Recherche: </b><?php echo htmlspecialchars( $this->metadata['Recherche']); ?><br />
			<?php }	?>
			<?php if( strlen( $this->metadata['Auszeichnungen'])) { ?>
				<b>Auszeichnungen: </b><?php echo htmlspecialchars( $this->metadata['Auszeichnungen']); ?><br />
			<?php }	?>
			<?php if( strlen( $this->metadata['Bemerkungen'])) { ?>
				<b>Bemerkungen: </b><?php echo htmlspecialchars( $this->metadata['Bemerkungen']); ?><br />
			<?php }	?>
			<?php if( strlen( $this->metadata['Kontakt']) && $session->inGroup( 'fhnw.ch/staff' )) { ?>
				<b>Kontakt: </b><br /><?php echo nl2br( htmlspecialchars( $this->metadata['Kontakt'])); ?><br />
			<?php }	?>
    </span>
		</div>
	</div>
	<div class="col-md-6">

  <?php if( strlen( $this->metadata['Pfad'] ) && strlen( $this->metadata['Bild1'] )) { ?>
		<div style="">
			<span style="; font-weight: bold;">Abbildungen</span><br />
		<div class="facet" style="padding-bottom: 5px;">
			<div class="marker" style=""></div>
      <table style="width: 100%;">
        <tr>
          <td rowspan="4" style="width: 80%;">
            <img style="max-width: 100%; max-height: 100%;" id="mainimg" src="<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild1'].".jpg"; ?>" />
          </td>
					<td style="width: 20%; height: 100px; background-image: url(<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild1'].".jpg"; ?>); background-size: cover;">
						<div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild1'].".jpg"; ?>');">
						</div>
          </td>
        </tr>
        <tr>
					<td style="width: 20%; height: 100px; background-image: url(<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild2'].".jpg"; ?>); background-size: cover;">
						<div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild2'].".jpg"; ?>');">
						</div>
          </td>
				</tr>
				<tr>
          <td style="width: 20%; height: 100px; background-image: url(<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild3'].".jpg"; ?>); background-size: cover;">
						<div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild3'].".jpg"; ?>');">
						</div>
          </td>
				</tr>
				<tr>
					<td style="height: 100%;"></td>
        </tr>
      </table>
	</div>
</div>
<?php
  }

?>
<div style="">
	<span style="; font-weight: bold;">Text</span><br />
<div class="facet" style="padding-bottom: 5px;">
	<div class="marker" style=""></div>
		<div class="media-body">
			<p style="text-align: right; margin-left: 90px; margin-right: 10px;"><i><?php echo htmlspecialchars( $this->metadata['Abstract'] ); ?></i></p>
			<p><?php echo htmlspecialchars( $this->metadata['TextZurArbeit'] ); ?></p>
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
						foreach( $this->doc->cluster_ss as $i=>$cl ) {
?>
								<label>
									<a href="javascript:doSearchFull('', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );">
										<?php echo ( $cl ); ?>
									</a>
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
	<div class="col-md-9">
	<div style="">
	<span style="; font-weight: bold;">Weitere Objekte aus Modul "<?php echo $this->metadata['Modul']['Titel']; ?>"</span><br />
		<div class="facet" style="">
			<div class="marker" style=""></div>
	<?php
	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$qstr = 'issue:'.$helper->escapePhrase( $this->metadata['Modul']['Kennziffer'].' - '.$this->metadata['Modul']['Titel'] );
	$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
	//echo "\n<!-- {$qstr} -->\n";
	$squery->setQuery( $qstr );
	$rs = $solrclient->select( $squery );
	$numResults = $rs->getNumFound();
	$numPages = floor( $numResults / 500 );
	if( $numResults % 500 > 0 ) $numPages++;

	echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".(1)." of {$numPages}) -->\n";

	$res = new DesktopResult( $rs, 0, 500, $this->db, $urlparams );
	echo $res->getResult();

	?>
		</div>
	</div>

	<?php
	if( DEBUG ) {
		?>
		<div style="">
		<span style="; font-weight: bold;">RAW Data</span><br />
			<div class="facet" style="padding: 0px;">
				<div class="marker" style=""></div>
				<div>
					<pre>
						<?php print_r( $this->metadata ); ?>
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
						<?php print_r( $this->doc ); ?>
					</pre>
				</div>
			</div>
		</div>
	<?php } ?>
		</div>
	</div>
</div>
		<script>
			function initHGKWeb() {

			}
		</script>

<?php

//		echo "<code>".nl2br(str_replace( ' ', '&nbsp;', print_r( $this->metadata, true )))	."</code>\n";
        $html .= ob_get_contents();
        ob_end_clean();
		return $html;
	}

    public function desktopList() {
    	global $config;


		$html = '';
		$t = strtolower( $this->doc->type );
    $authors = array_unique( $this->entity->getAuthors());

		$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                  <?php if( count( $authors )) echo htmlspecialchars( $authors[0]  );
                   if( count( $authors ) > 1) echo " et al."; ?>
                </a>
            </td>
            <td class="list" style="width: 5%; font-size: 20px;"><i class="<?php echo $icon; ?>"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $this->doc->title ); ?>
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
                  				if( count( $authors ) > 0 ) echo "<br />\n";
                  ?>
                  <?php echo $this->metadata['Kurzbeschreibung']; ?><br />
                    <?php echo 'Jahr: '.htmlspecialchars( $this->entity->getYear())."<br />\n"; ?>
					<!--<i class="fa fa-external-link" aria-hidden="true"></i> <a href="http://www.imdb.com/find?ref_=nv_sr_fn&q=<?php echo urlencode(trim($this->metadata['Titel1'] )); ?>&s=all" target="_blank"><img src="img/imdb.png"></a><br /> -->
                    <?php if( strlen( $this->metadata['Pfad']) && strlen( $this->metadata['Bild1'])) echo "<img src=\"".$config['iid']['mediaurl'].$this->metadata['Pfad'].'/'.$this->metadata['Bild1'].".jpg\" width=200 /><br />\n"; ?>

					ID: <?php echo $this->doc->id; ?><br />
					<a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>"><i class="fa fa-folder-open" aria-hidden="true"></i> Details</a><br />

                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
