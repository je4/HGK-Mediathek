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

class videokunst_ch_workDisplay extends DisplayEntity {
	private $metadata;
  private $entity;

    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->entity = new videokunst_ch_workEntity( $this->db );
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
				<h2 class="small-heading">videokunst.ch</h2>

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
			<h5><span style="font-weight: normal; font-size: 1.1rem;"><?php
      $first = true;
					$author = $this->metadata['artistname'];
					?>
					<a href="javascript:doSearchFull('author:&quot;<?php echo htmlspecialchars( $author ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
						<?php echo htmlspecialchars( $author ); ?>
					</a></span>
					<?php

        echo '<br />'.htmlentities( $this->metadata['vidtitel'] );
				if( $this->entity->getYear() ) {
						?>
							<span style="font-size: 80%;">(<?php echo htmlspecialchars( $this->entity->getYear() );?>)</span>
						<?php
				}
				?>

      </h5>
    </span>
		</div>
	</div>
	<div class="col-md-6">

    <?php if( strlen( $this->metadata['vidtext'])) { ?>
		<div style="">
			<span style="; font-weight: bold;">Werk</span><br />
		<div class="facet" style="padding-bottom: 5px;">
			<div class="marker" style=""></div>
<?php
	if( file_exists( "{$config['videokunst.ch']['mediapath']}/{$this->doc->originalid}_00003.png" )) {
		$shots = glob( "{$config['videokunst.ch']['mediapath']}/{$this->doc->originalid}_*" );
		$n = max( floor( count( $shots ) / 3 ), 1 );
//		print_r( $shots );
//		echo "{$n}\n";
		$diff = -1;
		if( $n == 1 && count( $shots ) > 3 ) $diff = 0;
?>
	<center>
      <table style="width: 100%">
        <tr>
          <td rowspan="4" style="width: 80%; padding-right: 5px; text-align: right;">
            <img style="max-width: 100%; height: 288px;" id="mainimg" src="<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>" />
          </td>
					<td style="width: 20%; height: 96px; background-image: url(<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>); background-size: cover;">
						<div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>');">
						</div>
          </td>
        </tr>
        <tr>
					<td style="width: 20%; height: 96px; background-image: url(<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[2*$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>); background-size: cover;">
						<div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[2*$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>');">
						</div>
          </td>
				</tr>
				<tr>
          <td style="width: 20%; height: 96px; background-image: url(<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[3*$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>); background-size: cover;">
						<div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $config['videokunst.ch']['mediaurl'].substr( $shots[3*$n+$diff], strlen( "{$config['videokunst.ch']['mediapath']}" )); ?>');">
						</div>
          </td>
				</tr>
				<tr>
					<td style="height: 100%;"></td>
        </tr>
      </table>
		</center>
<?php
	}
?>
	&nbsp;<br />
	<b>Direktlink auf videokunst.ch</b><br />
	<i class="fa fa-external-link" aria-hidden="true"></i><a href="redir.php?id=<?php echo urlencode( $this->doc->id ).'&url='.urlencode( $this->metadata['vidhtml'] )."\" target=\"blank\">".htmlspecialchars( $this->metadata['vidhtml'] ); ?></a><br />
</div>
</div>
<div style="">
	<span style="; font-weight: bold;">Beschreibung</span><br />
<div class="facet" style="padding-bottom: 5px;">
	<div class="marker" style=""></div>

			<?php echo $this->metadata['vidtext']; ?><br />
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
	<span style="; font-weight: bold;">Weitere Inhalte von "<?php echo $this->metadata['artistname']; ?>"</span><br />
		<div class="facet" style="">
			<div class="marker" style=""></div>
	<?php
	$squery->setRows( 500 );
	$squery->setStart( 0 );
	$qstr = 'author:'.$helper->escapePhrase( $this->metadata['artistname'] );
//	$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
	//echo "\n<!-- {$qstr} -->\n";
	$squery->setQuery( $qstr );
	$squery->createFilterQuery('source')->setQuery('source:videokunst_ch_work OR source:videokunst_ch_person');
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
			function initvideokunst_ch_work() {

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
                <a class="entity" href="#coll_<?php echo str_replace( '.', '_', $this->doc->id); ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo str_replace( '.', '_', $this->doc->id); ?>">
                  <?php if( count( $authors )) echo htmlspecialchars( $authors[0]  );
                   if( count( $authors ) > 1) echo " et al."; ?>
                </a>
            </td>
            <td class="list" style="width: 5%; font-size: 20px;"><i class="<?php echo $icon; ?>"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo str_replace( '.', '_', $this->doc->id); ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo str_replace( '.', '_', $this->doc->id); ?>">
                    <?php echo htmlspecialchars( $this->doc->title ); ?>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo str_replace( '.', '_', $this->doc->id); ?>">
									<?php
										if( file_exists( "{$config['videokunst.ch']['mediapath']}/{$this->doc->originalid}_00003.png" )) {
											?><img width="120" src="<?php echo "{$config['videokunst.ch']['mediaurl']}/{$this->doc->originalid}_00003.png"; ?>" /><br /><?php
										}
									?>
                  <?php
                  				$first = true;
                  				foreach( $authors as $author ) {
                  					echo ($first?"":"; ").htmlspecialchars( $author );
                  					$first = false;
                  				}
                  				if( count( $authors ) > 0 ) echo "<br />\n";
                  ?>
					<!--<i class="fa fa-external-link" aria-hidden="true"></i> <a href="http://www.imdb.com/find?ref_=nv_sr_fn&q=<?php echo urlencode(trim($this->metadata['Titel1'] )); ?>&s=all" target="_blank"><img src="img/imdb.png"></a><br /> -->
					<i class="fa fa-external-link" aria-hidden="true"></i><a href="redir.php?id=<?php echo urlencode( $this->doc->id ).'&url='.urlencode( $this->metadata['vidhtml'] )."\" target=\"blank\">".htmlspecialchars( $this->metadata['vidhtml'] ); ?></a><br />

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
