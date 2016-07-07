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
	
    public function __construct( $doc, $urlparams, $db ) {
        parent::__construct( $doc, $urlparams, $db );
		
		$this->metadata = (array) json_decode( $this->data );
    }

	public function detailView() {
        global $config, $googleservice, $googleclient, $session;
		
		$cert = $session->inGroup( 'certificate/mediathek');
		$intern = $session->inGroup( 'location/fhnw');
		$loggedin = $session->isLoggedIn();
		
		$html = '';
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
<h3><?php if( isset( $this->metadata['Autor Regie'] )) echo htmlspecialchars( $this->metadata['Autor Regie'].': ' ); echo htmlentities( $this->metadata['Titel1'] );?> <span style="font-size: 80%;"><?php echo htmlspecialchars( $this->metadata['Produktionsjahr'] );?></span></h3>
<h4><?php if( isset( $this->metadata['Titel2'] )) echo htmlspecialchars( $this->metadata['Titel2'].'.' );?>
	<span style="font-size: 80%;"><?php if( isset( $this->metadata['Dauer'] )) echo htmlspecialchars( $this->metadata['Dauer'] );?>
	<?php if( isset( $this->metadata['Land'] )) echo htmlspecialchars( $this->metadata['Land'] );?>
	</span></h4>
<?php
		
		//print_r( $session->getGroups());
		// Fall 1
		if( !$loggedin /* && !$cert */ ) {
?>
<div class="media">
	<a class="media-left" href="#">
			<!--
			<video id="my-video" class="video-js" autoplay controls preload="auto" width="188" height="150"
			poster="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00001.thumb.png'; ?>" data-setup="{}">
			  <source src="<?php echo $config['media']['videoopen'].'/'.intval($this->doc->originalid).'.thumb.mp4'; ?>" type='video/mp4'>
			  <p class="vjs-no-js">
				To view this video please enable JavaScript, and consider upgrading to a web browser that
				<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
			  </p>
			</video>
			-->
			<img class="media-left" src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00001.thumb.png'; ?>" />
	</a>
	<div class="media-body">
		<p><?php echo htmlspecialchars( $this->metadata['Bemerkungen'] ); ?></p>
	</div>
</div>

<?php
		}
		elseif( $session->inGroup( 'certificate/mediathek') && $this->doc->embedded ) {
?>
  <video id="my-video" class="video-js" controls preload="auto" width="720" height="576"
  poster="<?php echo $config['media']['picintern'].'/'.intval($this->doc->originalid).'.00001.big.png'; ?>" data-setup="{}">
    <source src="<?php echo $config['media']['videocert'].'/'.intval($this->doc->originalid).'.mp4'; ?>" type='video/mp4'>
    <p class="vjs-no-js">
      To view this video please enable JavaScript, and consider upgrading to a web browser that
      <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video>
  
<?php
		}

?>
<p>
&nbsp;<br />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00005.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00010.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00015.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00020.thumb.png'; ?>" />
<img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00025.thumb.png'; ?>" />
</p>

<!--
<div id="carousel-shot" class="carousel slide" data-ride="carousel" style="width:188px;">
  <ol class="carousel-indicators">
    <li data-target="#carousel-shot" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-shot" data-slide-to="1"></li>
    <li data-target="#carousel-shot" data-slide-to="2"></li>
    <li data-target="#carousel-shot" data-slide-to="3"></li>
    <li data-target="#carousel-shot" data-slide-to="4"></li>
  </ol>
  <div class="carousel-inner" role="listbox">
    <div class="carousel-item active">
      <img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00005.thumb.png'; ?>" alt="Videoshot #1">
    </div>
    <div class="carousel-item">
      <img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00010.thumb.png'; ?>" alt="Videoshot #2">
    </div>
    <div class="carousel-item">
      <img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00015.thumb.png'; ?>" alt="Videoshot #3">
    </div>
    <div class="carousel-item">
      <img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00020.thumb.png'; ?>" alt="Videoshot #4">
    </div>
    <div class="carousel-item">
      <img src="<?php echo $config['media']['picopen'].'/'.intval($this->doc->originalid).'.00025.thumb.png'; ?>" alt="Videoshot #5">
    </div>
  </div>
  <a class="left carousel-control" href="#carousel-shot" role="button" data-slide="prev">
    <span class="icon-prev" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-shot" role="button" data-slide="next">
    <span class="icon-next" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
-->
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
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $this->doc->author[0] ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%; font-size: 20px;"><i class="ion-ios-videocam"></i></td>
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