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

include( 'encoding.php' );

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
			$title = null;
			$url = null;
			$lib = $this->item->getLibrary();
			$v = array();
			if( $lib ) $v = $lib->getVar( 'title' );
			if( count( $v) > 0 ) $title = $v[0];
			if( $title == null ) $title = $this->item->getLibraryName();
			$v = array();
			if( $lib ) $v = $lib->getVar( 'url' );
			if( count( $v) > 0 ) $url = $v[0];
?>
				<h2 class="small-heading"><?php echo ($url?"<a href=\"{$url}\">":'').htmlspecialchars($title).($url?'</a>':''); ?></h2>

				<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
			$html .= ob_get_contents();
			ob_end_clean();
	return $html;
}

private function beginBox( $title ) {
?>
<!-- BEGIN BOX **** <?php echo $title; ?> -->
			<span style="font-weight: bold;">
<?php
				echo htmlspecialchars( $title );
?>
		</span><br>
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
}

private function endBox( $title ) {
?>
			</div>
			<!-- END BOX **** <?php echo $title; ?> -->
<?php
}

	public function detailView() {
      global $config, $googleservice, $googleclient, $solrclient, $db, $urlparams, $pagesize, $session, $q;

		$intern = $session->inGroup( 'location/fhnw');
		$loggedin = $session->isLoggedIn();

//		$authors = $this->entity->getAuthors();
		$authors = $this->item->getCreators();
		$squery = $solrclient->createSelect();
		$helper = $squery->getHelper();
		$pdfs = $this->item->getPDFs();
		$refPdfs = array();
		$refMaps = array();

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
			<h5><span style="font-weight: normal; font-size: 1.1rem;"><?php
      $first = true;
        foreach( $authors as $author ) {
					// no castMember...
					if( $this->item->getType() == 'videoRecording' && $author->getType() == 'castMember' ) continue;
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
						?>, <span style="font-size: 80%;"><?php echo htmlspecialchars( $this->item->getYear() );?></span>
						<?php
				}
				?>
      </h5>
			<div style="line-height: 110%">
			<?php
			$series = $this->item->get( 'seriesTitle' );
			if( $series ) echo htmlspecialchars( $series )."<br />\n";
			$place = $this->item->get( 'place' );
			if( $place ) {
?>
				<a href="javascript:doSearchFull('city:&quot;<?php echo htmlspecialchars( $place ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
					<?php echo htmlspecialchars( $place ); ?>
				</a><br />
<?php
			}

			if( $this->item->getType() == 'videoRecording') {
				$cs = array();
				foreach( $this->item->getCreators() as $c ) {
					// only castMember
					if( $c->getType() == 'castMember' ) {
?>
						Dokumentation: <a href="javascript:doSearchFull('author:&quot;<?php echo htmlspecialchars( $c ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
							<?php echo htmlspecialchars( $c ); ?>
						</a><br />
<?php
					}
				}
			}
	
			if( $this->item->getUrl()) { 
				?><i class="fa fa-external-link" aria-hidden="true"></i><a href="<?php echo $this->item->getUrl(); ?>" target="blank" style="word-wrap: break-word;"><?php echo htmlspecialchars( $this->item->getUrl()); ?></a>
				<br />
<?php 
			}
?>			

		</div>
    </span>
		</div>


		<?php
			$attachments = $this->item->getAttachments();
			usort( $attachments , function( $a, $b ) {
				return $a->getTitle() > $b->getTitle();
			});
		if( false ) foreach( $this->item->getImages() as $img ) {

?>
		<span style="; font-weight: bold;"><?php echo htmlspecialchars( $img->getTitle()); ?></span><br>
		<div class="facet" style="">
			<div class="marker" style=""></div>
			<img style="max-width: 100%;" src="zotero_data.php?id=zotero-<?php echo $img->getLibraryId(); ?>.<?php echo $this->item->getKey(); ?>&key=<?php echo $img->getKey(); ?>" />
		</div>
	<?php } ?>
	<?php
		foreach( $this->item->getNotes() as $note ) { ?>
	<span style="font-weight: bold;">Note</span><br>
	<div class="facet" style="">
		<div class="marker" style=""></div>
		<?php
		$str = strip_tags($note->getNote(), '<br><p>');
		$str = preg_replace('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', '<a href="$1">$1</a>', $str);		
		
		echo $str; 
		?>
	</div>
	<?php } ?>
	</div>
	<div class="col-md-6">
<?php
	$show = true;
	if( @is_array( $this->doc->acl_content )) {
		$show = $session->inAnyGroup( $this->doc->acl_content ) || $session->isAdmin();
		
	}
	if( $show ) {
		$images = [];
		foreach( $attachments as $att ) {
			if( in_array( $att->getLinkMode(), ['linked_url', 'imported_file'] )) {
				$url = $att->getUrl();
				$mime = $att->getUrlMimetype();

//					echo "mime: {$mime} ";
				if( preg_match( '/^mediaserver:(.+)$/', $url, $matches )) {
					$media = $att->getMedia();
					$type = null;
					if( isset( $media['metadata']['type'] ))	{
						$type = $media['metadata']['type'];
					}
					$copyright = null;
					if( isset( $media['metadata']['image']['properties']['exif:Copyright'] ))	{
						$copyright = $media['metadata']['image']['properties']['exif:Copyright'];
					}
					if( isset( $media['metadata']['exif'][0]['Copyright'] ))	{
						$copyright = $media['metadata']['exif'][0]['Copyright'];
					}
					$copyright = \ForceUTF8\Encoding::fixUTF8($copyright);
					$orientation = 1;
					if( isset( $media['metadata']['image']['properties']['exif:Orientation'] ))	{
						$orientation = max(intval($media['metadata']['image']['properties']['exif:Orientation']), 1);
					}
					$width = 0;
					if( isset( $media['metadata']['width'] ))	{
						$width = $media['metadata']['width'];
					}
					$height = 0;
					if( isset( $media['metadata']['height'] ))	{
						$height = $media['metadata']['height'];
					}
					if( $type == 'image' ) {
						$_img = [
							'url'=>$url,
							'copyright'=>$copyright,
							'width'=>$width,
							'height'=>$height,
							'orientation'=>$orientation,
						 ];
						 switch( $orientation ) {
							 case 1:
							 case 2:
							 case 3:
							 case 4:
							 $_img['realwidth'] = $_img['width'];
							 $_img['realheight'] = $_img['height'];
							 break;
							 // rotation 90 or -90
							 // flip width and height
							 case 5:
							 case 6:
							 case 7:
							 case 8:
							 $_img['realwidth'] = $_img['height'];
							 $_img['realheight'] = $_img['width'];
							 break;
						 }
						$images[] = $_img;
//						$link = $this->mediaLink( $url.'/resize/size800x1000' );
//						$imgserver = $this->mediaLink( $url.'/iframe' );
					}
				}
			}
		}
		$doGallery = count( $images ) > 2;
		if( $doGallery ) {
		// now lets build three cols
		$cols = [[], [], []];
		$colheight = [0, 0, 0];
		foreach( array_keys( $images ) as $idx ) {
//		foreach( $images as $img ) {
			$img = $images[$idx];
			$minheight = min($colheight);
			$col = array_search($minheight, $colheight);
			$cols[$col][] = $img;
			$colheight[$col] = $colheight[$col] + round( $img['height']*200/$img['width'] ) + 10;
		}
//		echo "<!-- GALLERY \n";
//		print_r( $colheight );
//		print_r( $cols );
//		echo "-->\n";

		$title = "Gallery";
		$this->beginBox( $title );
?>
<style>
<?php 
/*
.exif-orientation-1 {
}

.exif-orientation-2 {
  -webkit-transform: scaleX(-1);
  -moz-transform: scaleX(-1);
  -o-transform: scaleX(-1);
  -ms-transform: scaleX(-1);
  transform: scaleX(-1);
}

.exif-orientation-3 {
	-webkit-transform: rotate(180deg);
	-moz-transform: rotate(180deg);
	-o-transform: rotate(180deg);
	-ms-transform: rotate(180deg);
	transform: rotate(180deg);
}

.exif-orientation-4 {
	-webkit-transform: rotate(180deg) scaleX(-1);
	-moz-transform: rotate(180deg) scaleX(-1);
	-o-transform: rotate(180deg) scaleX(-1);
	-ms-transform: rotate(180deg) scaleX(-1);
	transform: rotate(180deg) scaleX(-1);
}

.exif-orientation-5 {
	-webkit-transform: rotate(90deg);
	-moz-transform: rotate(90deg);
	-o-transform: rotate(90deg);
	-ms-transform: rotate(90deg);
	transform: rotate(90deg);
}

.exif-orientation-6 {
	-webkit-transform: rotate(90deg) scaleX(-1);
	-moz-transform: rotate(90deg) scaleX(-1);
	-o-transform: rotate(90deg) scaleX(-1);
	-ms-transform: rotate(90deg) scaleX(-1);
	transform: rotate(90deg) scaleX(-1);
}

.exif-orientation-7 {
	-webkit-transform: rotate(270deg);
	-moz-transform: rotate(270deg);
	-o-transform: rotate(270deg);
	-ms-transform: rotate(270deg);
	transform: rotate(270deg);
}

.exif-orientation-8 {
	-webkit-transform: rotate(270deg) scaleX(-1);
	-moz-transform: rotate(270deg) scaleX(-1);
	-o-transform: rotate(270deg) scaleX(-1);
	-ms-transform: rotate(270deg) scaleX(-1);
	transform: rotate(270deg) scaleX(-1);
}
*/
?>
.img-container {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
}

.img-row {
	justify-content: center !important;
	flex-wrap: wrap !important;
	flex-direction: row !important;
	display: flex !important;
}

.img-col {
	max-width: 260px;
	flex-direction: column !important;
	display: flex !important;
}

        img {
            margin: 5px;
					  transition: all 2s;
				}

				img:hover {
					 transform: scale (1.1);
				}

        .scale {
            transform: scaleY(1.05);
            padding-top: 5px;
        }
</style>
<div class="img-container">
  <div class="img-row">
<?php
	foreach( $cols as $col ) {
		echo '		<div class="img-col">'."\n";
		foreach( $col as $img ) {
			$link = $this->mediaLink( $img['url'].'/resize/size200x' );
			$biglink = $this->mediaLink( $img['url'].'/resize/size800x1000' );
			$imgserver = $this->mediaLink( $img['url'].'/iframe' );
		echo "<!-- {$img['url']}  --  {$link} -->\n";
			?>
					<a href="<?php echo $biglink; ?>" class="" data-toggle="lightbox" data-footer="<?php echo $img['copyright']; ?>" data-gallery="main_gallery" data-type="image"">
						<img width="200", height="<?php echo round( $img['height']*200/$img['width'] ); ?>" src="<?php echo $link; ?>" class="img-fluid exif-orientation-<?php echo $img['orientation']; ?>" />
					</a>
			<?php
		}
		echo '		</div>'."\n";
	}
?>
	</div>
</div>
<?php
		$this->endBox( $title );

}
		foreach( $attachments as $att ) {
		//if( !DEBUG && $att->getLinkMode() == 'linked_url' && $att->getUrlMimetype() == null ) continue;
		if( preg_match( '/^Parent:/', $att->getTitle())) continue;

		$title = $att->getTitle();
		if( is_numeric( $title ) ) $title = '';
//		elseif( preg_match( '/^[0-9]+([^0-9].*)/', $title, $matches )) {
//			$title = $matches[1];
//		}

		// ignore strange files
		if( preg_match( '/\.DS_Store/', $title )) continue;
		
		$title = trim($title, ' -.;');
//		$this->beginBox( $title );
//				var_dump( $att );
				if( in_array( $att->getLinkMode(), ['linked_url', 'imported_file'] )) {
					$url = $att->getUrl();
					$mime = $att->getUrlMimetype();
					if( $mime == null ) {
					}
					if( preg_match( '/^(mediaserver|mediasrv):(.+)$/', $url, $matches )) {
						$media = $att->getMedia();
//						var_dump( $media );
						$type = null;
						if( isset( $media['metadata']['type'] ))	$type = $media['metadata']['type'];
						if( $type == 'video' || $type == 'pdf' || $type == 'audio' || $type == 'gpx' ) {
							$this->beginBox( $title );

							//todo: goserver config, token basteln
							$link = $this->mediaLink( $url.'/iframe/bgcolorffffff' );
							?>
							<iframe style="width: calc(100% - 5px); height: <?php echo ($type == 'audio' ? '130px':'400px'); ?>; border:0;" border=0 src="<?php echo $link; ?>" class="video" allowfullscreen=""></iframe>
							<?php

							$this->endBox( $title );
							if( DEBUG && $loggedin ) {
								echo "<a href=\"{$link}\">{$link}</a><br />\n";
							} 
						}
						elseif( $type == 'image' ) {
							if( !$doGallery ) {
								$this->beginBox( $title );

								$link = $this->mediaLink( $url.'/resize/size800x1000' );
								$imgserver = $this->mediaLink( $url.'/iframe' );
								?>
								<a href="<?php echo $imgserver; ?>" target=_blank><img src="<?php echo $link; ?>" style="width: 100%;" /></a>
								<?php
								$this->endBox( $title );
							}
						}
						else {
							$this->beginBox( $title );
							echo "<a href=\"".$this->mediaLink( $url.'/master' )."\" target=_blank>{$url}</a>";
							$this->endBox( $title );
						}
					}
					elseif( preg_match( '/^image\//', $att->getUrlMimetype())) {
						$this->beginBox( $title );
?>
					<center>
						<img src="<?php echo $config['imageserver'].$att->getNameImageserver().'/640x480/fit'; ?>" />
					</center>
<?php
						$this->endBox( $title );
					}
					elseif( preg_match( '/^application\/pdf/', $att->getUrlMimetype())) {
						$this->beginBox( $title );

						$refPdfs[] = $att;
?>
											<div id="pdf_<?php echo $att->getLibraryId(); ?>_<?php echo $att->getKey(); ?>">
											</div>
											<div style="text-align: center;">
												<a href="<?php echo $config['imageserver'].$att->getNameImageserver(); ?>" >Download PDF</a></div>
<?php
						$this->endBox( $title );
					}
					elseif( preg_match( '/^text\/html/', $att->getUrlMimetype()) && preg_match( '/\.gpx$/', $url )) {
						$this->beginBox( $title );

							$refMaps[] = $att->getLibraryId().'_'.$att->getKey();
?>
										<link rel="stylesheet" href="https://openlayers.org/en/v4.0.1/css/ol.css" type="text/css">
										<div style="width:99%; height:600px" id="map_<?php echo $att->getLibraryId(); ?>_<?php echo $att->getKey(); ?>" class="map"></div>
										<select id="layer-select_<?php echo $att->getLibraryId(); ?>_<?php echo $att->getKey(); ?>">
											<option value="Aerial">Luftbild</option>
											<option value="AerialWithLabels" selected>Luftbild mit Beschriftung</option>
											<option value="Road">Strasse</option>
										</select>
										<div id="info_<?php echo $att->getLibraryId(); ?>_<?php echo $att->getKey(); ?>"> </div>
										<p><hr /></p>
										<a href="<?php echo $config['imageserver'].$att->getNameImageserver(); ?>" target="_blank">Download File</a>
<?php
							$this->endBox( $title );
					}
					elseif( strstr( $url, 'https://ba14ns21403.fhnw.ch/video') !== false ) {
						$this->beginBox( $title );
?>
						<video id="my-video" class="video-js" controls preload="auto" width="550"
						data-setup="{}"
						style="min-width: 99%" >
						  <source src="<?php echo $url; ?>" type='video/mp4'>
						  <p class="vjs-no-js">
							To view this video please enable JavaScript, and consider upgrading to a web browser that
							<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
						  </p>
						</video>
<?php
						$this->endBox( $title );
					}
					elseif( $mime == null) {
						$this->beginBox( $title );
						?>
							Invalid URL: <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
						<?php
						$this->endBox( $title );
					}
					else {
						$this->beginBox( $title );
						?>
							<i class="fa fa-external-link" aria-hidden="true"></i><a href="<?php echo $url; ?>" target="blank" style="word-wrap: break-word;"><?php echo htmlspecialchars( $url ); ?></a>
						<?php
						$this->endBox( $title );
					}
				} // linkMode == linkedURL
				elseif( preg_match( '/application\/pdf/', $att->getContentType())) {
					$this->beginBox( $title );
?>
					<div id="pdf_<?php echo $att->getLibraryId(); ?>_<?php echo $att->getKey(); ?>">
					</div>
					<div style="text-align: center;"><a href="zotero_data.php?id=zotero-<?php echo $att->getLibraryId(); ?>.<?php echo $this->item->getKey(); ?>&key=<?php echo $att->getKey(); ?>" >Download PDF</a></div>
<?php
					$this->endBox( $title );
				}
				else {
					$this->beginBox( $title );

					$url = $att->getUrl();
					if( strlen( $url ) && substr( $url, 0, strlen( 'mediaserver:')) != 'mediaserver:' ) echo "<a href=\"{$url}\" target=_blank>{$url}</a>\n";
					$this->endBox( $title );
				}

			if( false ) {
			 ?>
			<!--
			<pre>
				<?php print_r( $att->getData()); ?>
			</pre>
		  -->
	<?php
			}
//	   $this->endBox( $title );
	}
}

?>
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
						<?php  }
				$rels = $this->item->getRelations();
				if( is_array( $rels ) && count( $rels ))  { ?>
				<div style="">
				<span style="; font-weight: bold;">Verweise</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
						foreach( $rels as $rel ) {
							if( preg_match( '/^(.+):http:\/\/zotero.org\/groups\/([0-9]+)\/items\/([A-Z0-9]+)$/', $rel, $matches)) {
?>
								<label>
									<a href="<?php echo "detail.php?id=zotero-{$matches[2]}.{$matches[3]}&q={$q}"; ?>">
										<?php echo "{$matches[2]}.{$matches[3]}"; ?>
									</a>
							</label><br />
<?php
						}
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
			$squery->setRows( 500 );
			$squery->setStart( 0 );
			$qstr = '';
			foreach( $categories as $key=>$cat ) {
				$qstr .= ($key == 0 ? '' : ' OR ').'category: '.$helper->escapePhrase($cat);
			}
			$qstr = trim( $qstr );
			if( strlen( $qstr )) $qstr = "({$qstr}) AND ";
			$qstr .= "-id:".$helper->escapeTerm( $this->doc->id );
			$qstr = "{$qstr} AND catalog:".$helper->escapePhrase( $libname );
			echo "\n<!-- {$qstr} -->\n";
			$squery->setQuery( $qstr );
	//		$squery->createFilterQuery('source')->setQuery('source: zotero');
			$rs = $solrclient->select( $squery );
			$numResults = $rs->getNumFound();
			$numPages = floor( $numResults / 500 );
			if( $numResults % 500 > 0 ) $numPages++;

			echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".(1)." of {$numPages}) -->\n";
			if( $numResults ) {
?>
		<div style="">
		<span style="; font-weight: bold;">Weitere Inhalte aus "<?php echo $libname.' - '.implode( '; ', $collections ); ?>"</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
		<?php
		$res = new DesktopResult( $rs, 0, 500, $this->db, $urlparams );
		echo $res->getResult();

		?>
			</div>
		</div>

	<?php
	}
	if( DEBUG && $loggedin ) {
		?>
		<div style="">
		<span style="; font-weight: bold;">CSL Data</span><br />
			<div class="facet" style="padding: 0px;">
				<div class="marker" style=""></div>
				<div>
					<pre>
						<?php echo ( $this->entity->cite( 'apa.csl', 'ch', 'bibliography' ) ); ?> (apa)
						<hr />
						<?php echo ( $this->entity->cite( 'chicago-annotated-bibliography.csl', 'ch', 'bibliography' ) ); ?> (chicago)
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
		<?php foreach( $refPdfs as $pdf )	{
		?>
					var container = $("#pdf_<?php echo $pdf->getLibraryId(); ?>_<?php echo $pdf->getKey(); ?>");

					var pdf = '<?php echo $config['imageserver'].$pdf->getNameImageserver(); ?>';

		    var options = {height: 700, duration: 800};

		    var flipBook = container.flipBook(pdf, options);

		<?php } ?>
				$(document).on('click', '[data-toggle="lightbox"]', function(event) {
				                event.preventDefault();
				                $(this).ekkoLightbox();
				            });
			}
		</script>


<?php

        $html .= ob_get_contents();
        ob_end_clean();
				addJS( "js/dflip/dflip.js");
				addJS( "js/ekko-lightbox.js" );
		return $html;
	}

    public function desktopList() {
    	global $config;


			$item = $this->entity->getItem();

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
				if( $this->item->getYear() ) {
						?>
							Jahr: <?php echo htmlspecialchars( $this->item->getYear() );?><br />
						<?php
				}									
                  ?>
					<?php if( $this->item->getUrl()) { ?>
					
					<i class="fa fa-external-link" aria-hidden="true"></i><a href="<?php echo $this->item->getUrl(); ?>" target="blank" style="word-wrap: break-word;"><?php echo htmlspecialchars( $this->item->getUrl());?></a>
					
					<br />
          <?php }

						$cat = $item->getLibraryCatalog();
						if( strlen( $cat )) echo "Quelle: ".htmlspecialchars( $cat )."<br />\n";
					?>

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
