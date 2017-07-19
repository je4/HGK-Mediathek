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

class zoteroDisplay extends DisplayEntity {
	private $metadata;
  private $entity;
  private $item;

    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->entity = new zoteroEntity( $this->db );
        $this->entity->loadFromDoc( $this->doc );

		    $this->metadata = $this->entity->getData();
        $this->item = $this->entity->getItem();
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
				<h2 class="small-heading">Mediathek</h2>

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
		$pdfs = $this->item->getPDFs();

		$html = '';

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
<link type="text/css" rel="stylesheet" href="css/dflip/dflip.css">
<link type="text/css" rel="stylesheet" href="css/dflip/themify-icons.css">

<div class="row">
	<div class="col-md-3">
		<span style="; font-weight: bold;">Aktuelles Objekt</span><br>
		<div class="facet" style="">
			<div class="marker" style=""></div>
      <?php echo $this->item->getType(); ?>
			<h5><span style="font-weight: normal; font-size: 1.1rem;"><?php
      $first = true;
        foreach( $authors as $author ) {
          if( !$first ) echo "; ";
					?>
					<a href="javascript:doSearchFull('author:&quot;<?php echo htmlspecialchars( $author ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
						<?php echo htmlspecialchars( $author ); ?>
					</a>
					<?php
          $first = false;
        }
        ?>
        </span>
        <?php
        echo '<br />'.htmlentities( $this->item->getTitle() );
				if( $this->item->getYear() ) {
						?>
							<span style="font-size: 80%;">(<?php echo htmlspecialchars( $this->item->getYear() );?>)</span>
						<?php
				}
				?>

      </h5>
    </span>
		</div>
		<?php foreach( $this->item->getImages() as $img ) { ?>
		<span style="; font-weight: bold;"><?php echo htmlspecialchars( $img->getTitle()); ?></span><br>
		<div class="facet" style="">
			<div class="marker" style=""></div>
			<img style="max-width: 100%;" src="zotero_data.php?id=zotero-<?php echo $img->getLibraryId(); ?>.<?php echo $this->item->getKey(); ?>&key=<?php echo $img->getKey(); ?>" />
		</div>
	<?php } ?>
	<?php
		foreach( $this->item->getNotes() as $note ) { ?>
	<span style="; font-weight: bold;">Note</span><br>
	<div class="facet" style="">
		<div class="marker" style=""></div>
		<?php echo $note->getNote(); ?>
	</div>
	<?php } ?>
	</div>
	<div class="col-md-6">
<?php
	$show = true;
	if( @is_array( $this->doc->acl_content )) {
		$show = $session->inAnyGroup( $this->doc->acl_content ) || $session->isAdmin();
	}
	if( $show ) foreach( $pdfs as $pdf ) {
?>
	<div style="">
		<span style="; font-weight: bold;">Preview</span><br />
	<div class="facet" style="padding-bottom: 5px;">
		<div class="marker" style=""></div>
		<div id="pdf_<?php echo $pdf->getLibraryId(); ?>_<?php echo $pdf->getKey(); ?>">
		</div>
		<div style="text-align: center;"><a href="zotero_data.php?id=zotero-<?php echo $pdf->getLibraryId(); ?>.<?php echo $this->item->getKey(); ?>&key=<?php echo $pdf->getKey(); ?>" >Download PDF</a></div>
	</div>
</div>

<?php } elseif( count( $pdfs ) > 0 ) { ?>
	<div style="">
		<span style="; font-weight: bold;">Keine Berechtigung</span><br />
	<div class="facet" style="padding-bottom: 5px;">
		<div class="marker" style=""></div>
		Um die Inhalte zu sehen, müssen Sie Mitglied einer der folgenden Gruppen sein:<br />
		<ul>
<?php foreach( $this->doc->acl_content as $grp ) echo "<li>{$grp}</li>\n"; ?>
		</ul>
		<?php if( !$session->isLoggedIn() ) echo "<b>Hinweis: Sie sind nicht angemeldet.</b>\n"; ?>
	</div>
</div>
<?php } ?>
    <?php if( strlen( $this->item->getAbstract())) { ?>
		<div style="">
			<span style="; font-weight: bold;">Abstract</span><br />
		<div class="facet" style="padding-bottom: 5px;">
			<div class="marker" style=""></div>
      <div style="word-wrap:break-word;">
<?php
  echo nl2br( $this->item->getAbstract());
?>
  </div>
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
	<div class="col-md-12">
		<p />
		<div style="">
<?php
			$categories = array();
			$collections = array();
			$libname = $this->item->getLibraryName();
			foreach( $this->item->getCollections() as $coll ) {
				$fname = $coll->getFullName();
				$parts = count( explode( ':', $fname ));

				$categories[] = ($parts+3).'!!fhnw!!hgk!!pub!!'.$libname.'!!'.str_replace( ':', '!!', $fname );
				$collections[] = $fname;
			}
?>
		<span style="; font-weight: bold;">Weitere Inhalte aus "<?php echo $libname.' - '.implode( '; ', $collections ); ?>"</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
		<?php
		$squery->setRows( 500 );
		$squery->setStart( 0 );
		$qstr = '';
		foreach( $categories as $key=>$cat ) {
			$qstr .= ($key == 0 ? '' : ' OR ').'category: '.$helper->escapePhrase($cat);
		}
		if( strlen( $qstr )) $qstr = "({$qstr})";
	//	$qstr = "({$qstr}) AND -id:".$helper->escapeTerm( $this->doc->id );
		//echo "\n<!-- {$qstr} -->\n";
		$squery->setQuery( $qstr );
//		$squery->createFilterQuery('source')->setQuery('source: zotero');
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

	if( DEBUG && $loggedin ) {
		?>
		<div style="">
		<span style="; font-weight: bold;">CSL Data</span><br />
			<div class="facet" style="padding: 0px;">
				<div class="marker" style=""></div>
				<div>
					<pre>
						<?php echo ( $this->entity->cite( 'apa.csl', 'ch', 'bibliography' ) ); ?>
						<hr />
						<?php echo ( $this->entity->cite( 'chicago-annotated-bibliography.csl', 'ch', 'citation' ) ); ?>
						<hr />
						<?php var_dump(  $this->entity->getCSL()); ?>
					</pre>
				</div>
			</div>
		</div>
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
			function initzotero() {
<?php foreach( $pdfs as $pdf )	{
		$pages = $pdf->getPages();
?>
			var pages = <?php echo $pages; ?>;
			var container = $("#pdf_<?php echo $pdf->getLibraryId(); ?>_<?php echo $pdf->getKey(); ?>");

			var pdf = 'zotero_data.php?id=zotero-<?php echo $pdf->getLibraryId(); ?>.<?php echo $this->item->getKey(); ?>&key=<?php echo $pdf->getKey(); ?>';

    var options = {height: 700, duration: 800};

    var flipBook = container.flipBook(pdf, options);

<?php } ?>

			}
		</script>

<?php

        $html .= ob_get_contents();
        ob_end_clean();
				addJS( "js/dflip/dflip.js");
		return $html;
	}

    public function desktopList() {
    	global $config;


		$html = '';
		$t = strtolower( $this->doc->type );
    $authors = array_unique( $this->item->getCreators());

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
                  				$first = true;
                  				foreach( $authors as $author ) {
                  					echo ($first?"":"; ").htmlspecialchars( $author );
                  					$first = false;
                  				}
                  				if( count( $authors ) > 0 ) echo "<br />\n";
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
                  ?>
					<?php if( $this->item->getUrl()) { ?><i class="fa fa-external-link" aria-hidden="true"></i><a href="redir.php?id=<?php echo urlencode( $this->doc->id ).'&url='.urlencode( $this->item->getUrl())."\" target=\"blank\">".htmlspecialchars( $this->item->getUrl() )."</a>"; ?>
					<br />
          <?php } ?>

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
