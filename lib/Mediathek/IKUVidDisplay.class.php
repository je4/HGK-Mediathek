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

class IKUVidDisplay extends DisplayEntity {
	private $metadata;
	
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );
		
		$this->metadata = (array) json_decode( $this->data );
    }

	public function getSchema() {
		global $config;
		
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array('Movie');
		$schema['@id'] = $this->doc->id;
		$schema['name'] = $this->doc->title;
		if( $this->doc->embedded ) {	
			$schema['image'] = $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00005.thumb.png';
		}		
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
  
	public function detailView() {
        global $config, $googleservice, $googleclient, $session, $page, $pagesize;
		
		$cert = $session->inGroup( 'certificate/mediathek');
		$intern = $session->inGroup( 'location/fhnw');
		$loggedin = $session->isLoggedIn();
		
		$html = '';
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
		$handle = 'https://hdl.handle.net/20.500.11806/mediathek/'.$this->doc->id;
?>
<div class="row">
	<div class="col-md-3">
		<span style="; font-weight: bold;">Aktueller Film</span><br>
		<div class="facet" style="">
			<div class="marker" style=""></div>
			<h5><?php if( isset( $this->metadata['Autor Regie'] )) echo htmlspecialchars( $this->metadata['Autor Regie'].': ' ); echo htmlentities( $this->metadata['Titel1'] );?> <span style="font-size: 80%;"><?php echo htmlspecialchars( $this->metadata['Produktionsjahr'] );?></span></h5>
			<b><?php if( isset( $this->metadata['Titel2'] )) echo htmlspecialchars( $this->metadata['Titel2'].'.' );?>
			<span style="font-size: 80%;"><?php if( isset( $this->metadata['Dauer'] )) echo htmlspecialchars( $this->metadata['Dauer'] );?>
			<?php if( isset( $this->metadata['Land'] )) echo htmlspecialchars( $this->metadata['Land'] );?>
			</span></b><br />
			Persistent Identifier: <a href="<?php echo $handle; ?>">https://hdl.handle...</a>
		</div>
	</div>
	<div class="col-md-6">
<?php
		
		//print_r( $session->getGroups());
		// Fall 1
		if( !$loggedin || !$this->doc->embedded/* && !$cert */ ) {
?>

<?php
		}
		elseif( $session->inAnyGroup( $this->doc->acl_content ) && $this->doc->embedded ) {
?>
	<span style="; font-weight: bold;">Film abspielen</span><br>
	<div class="facet" style="min-width: 550px; text-align: center;">
		<div class="marker" style=""></div>
		<video id="my-video" class="video-js" controls preload="auto" width="550"
		poster="<?php echo $config['media']['picintern'].'/'.intval($this->doc->originalid).'.00001.big.png'; ?>" data-setup="{}">
		  <source src="<?php echo $config['media']['videointern'].'/'.intval($this->doc->originalid).'.h264-1100k.mp4'; ?>" type='video/mp4'>
		  <p class="vjs-no-js">
			To view this video please enable JavaScript, and consider upgrading to a web browser that
			<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		  </p>
		</video>

	</div>  
<?php
		}
		else
		{
?>
	<span style="font-weight: bold;">Hinweis</span><br>
	<div class="facet" style="min-width: 740px; text-align: center;">
		<div class="marker" style=""></div>
		
		Abspielen von Videos ist nicht für alle Nutzer freigeschaltet. Bei Fragen wenden Sie sich bitte an die Mediathek.
	</div>
<?php
		}

?>
<br />
<div class="facet" style="padding-bottom: 5px;">
	<div class="marker" style=""></div>
		<div class="media-body">
			<p><?php echo htmlspecialchars( $this->metadata['Bemerkungen'] ); ?></p>
		</div>
	<?php if( $this->doc->embedded ) {	?>
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00005.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00009.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00013.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00017.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00021.thumb.png'; ?>" />
<?php } ?>
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
//						foreach( $entity->getCluster() as $i=>$cl ) {
//							echo htmlspecialchars( $cl ).'<br />';
?>
								<label>
									<a href="javascript:doSearchFull('', '', [], {<?php echo (DEBUG ? "'catalog':[".$this->getCatalogList()."]" : "'source':[".$this->getSourceList()."]"); ?>, cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
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
<!--						
				<div style="">
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
-->				
			</div>	
</div>
		<script>
			function initIKUVid() {
				
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
		$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $this->doc->author[0] ); ?>
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
                    <?php if( strlen( $this->metadata['Autor Regie'] )) echo 'Autor/Regie: '.htmlspecialchars( $this->metadata['Autor Regie'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Land'] )) echo 'Land: '.htmlspecialchars( $this->metadata['Land'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Produktionsjahr'] )) echo 'Jahr: '.htmlspecialchars( $this->metadata['Produktionsjahr'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Dauer'] )) echo 'Dauer: '.htmlspecialchars( $this->metadata['Dauer'] )."<br />\n"; ?>
					<!--<i class="fa fa-external-link" aria-hidden="true"></i> <a href="http://www.imdb.com/find?ref_=nv_sr_fn&q=<?php echo urlencode(trim($this->metadata['Titel1'] )); ?>&s=all" target="_blank"><img src="img/imdb.png"></a><br /> -->
					ID: <?php echo $this->doc->id; ?><br />
					<a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>"><i class="fa fa-folder-open" aria-hidden="true"></i> Details</a><br />

                </div>
            </td>
        </tr>
		<script src="js/video.js"></script>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}