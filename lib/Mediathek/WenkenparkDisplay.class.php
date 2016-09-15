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

class WenkenparkDisplay extends DisplayEntity {
	private $metadata;
	
    public function __construct( $doc, $urlparams, $db ) {
        parent::__construct( $doc, $urlparams, $db );
		
		$this->metadata = (array) json_decode( $this->data );
    }

	public function detailView() {
        global $config, $googleservice, $googleclient, $session, $page, $pagesize;
		
		$cert = $session->inGroup( 'certificate/mediathek');
		$intern = $session->inGroup( 'location/fhnw');
		$loggedin = $session->isLoggedIn();
		
		$html = '';
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
<div class="row full-height">
	<div class="col-md-3">
		<span style="; font-weight: bold;">Aktueller Film</span><br>
		<div class="facet" style="">
			<div class="marker" style=""></div>
			<h5><?php 
			$authors = array();
            if( strlen(trim( $this->metadata['AutorinVN1'])))
                 $authors[] = trim( $this->metadata['AutorinVN1']).' '.trim( $this->metadata['AutorInN1']);
            if( strlen(trim( $this->metadata['AutorinVN2'])))
                 $authors[] = trim( $this->metadata['AutorinVN2']).' '.trim( $this->metadata['AutorInN2']);
            if( strlen(trim( $this->metadata['AutorinVN3'])))
                 $authors[] = trim( $this->metadata['AutorinVN3']).' '.trim( $this->metadata['AutorInN3']);
			echo '<span style="font-weight: normal; font-size: 1.1rem;">';
			
			
			echo htmlentities( implode( ", ", $authors ));
			
			echo "</span>: ";
			echo htmlentities( $this->metadata['TITEL'] );
			?> <span style="font-size: 80%;">(<?php echo htmlspecialchars( $this->metadata['Produktionsjahr'] );?>)</span></h5>
			<span style="font-size: 80%; line-height: 1em;">
<?php 
					if( @strlen(trim( $this->metadata['KonzeptDrehbuchVor1'])))
						echo "Konzept/Drehbuch: ".htmlentities( trim( $this->metadata['KonzeptDrehbuchVor1']).' '.trim( $this->metadata['KonzeptDrehbuchNach1']))."<br />\n";
					if( @strlen(trim( $this->metadata['KonzeptDrehbuchVor2'])))
						echo "Konzept/Drehbuch: ".htmlentities( trim( $this->metadata['KonzeptDrehbuchVor2']).' '.trim( $this->metadata['KonzeptDrehbuchNach2']))."<br />\n";
					if( @strlen(trim( $this->metadata['KAMERA'])))
						echo "Kamera: ".htmlentities( trim( $this->metadata['KAMERA']))."<br />\n";
					if( @strlen(trim( $this->metadata['Schnitt'])))
						echo "Schnitt: ".htmlentities( trim( $this->metadata['Schnitt']))."<br />\n"; 
					if( @strlen(trim( $this->metadata['MUSIK'])))
						echo "Musik: ".htmlentities( trim( $this->metadata['MUSIK']))."<br />\n"; 
					if( @strlen(trim( $this->metadata['Ton'])))
						echo "Ton: ".htmlentities( trim( $this->metadata['Ton']))."<br />\n"; 
					if( isset( $this->metadata['LAENGE'] ))
						echo "Dauer: ".htmlspecialchars( substr( $this->metadata['LAENGE'], 0, -2 ).':'.substr( $this->metadata['LAENGE'], -2 ) )."<br />\n";;
					if( isset( $this->metadata['Ursprungsformat'] ))
						echo "Ursprungsformat: ".htmlspecialchars($this->metadata['Ursprungsformat'])."<br />\n";;
					$sys = array();
					if( isset( $this->metadata['TV System'] ))
						$sys[] = $this->metadata['TV System'];
					if( isset( $this->metadata['Farbe sw'] ))
						$sys[] = $this->metadata['Farbe sw'];
					if( isset( $this->metadata['Tonart'] ))
						$sys[] = $this->metadata['Tonart'];
					if( count( $sys )) echo htmlspecialchars( implode( ' / ', $sys ))."<br />\n";
					
?>
			</span>
		</div>
	</div>
	<div class="col-md-6">
<?php
		
		//print_r( $session->getGroups());
		// Fall 1
		if( !$loggedin || !$this->doc->embedded/* && !$cert */ ) {
?>
<!--
	<span style="; font-weight: bold;"></span><br>
	<div class="facet" style="">
		<div class="marker" style=""></div>
			<div class="media">
			<a class="media-left" href="#">
				<img class="media-left" src="<?php echo $config['media']['picopen'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00001.thumb.png'; ?>" />
			</a>
			<div class="media-body">
				<p><?php echo htmlspecialchars( $this->metadata['KURZBESCHRIEB'] ); ?></p>
			</div>
		</div>
	</div>
-->
<?php
		}
		elseif( $session->inAnyGroup( $this->doc->acl_content ) && $this->doc->embedded ) {
?>
	<span style="; font-weight: bold;">Film abspielen</span><br>
	<div class="facet" style="min-width: 550px; text-align: center;">
		<div class="marker" style=""></div>
		<video id="my-video" class="video-js" controls preload="auto" width="550"
		poster="<?php echo $config['media']['picintern'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00001.big.png'; ?>" data-setup="{}">
		  <source src="<?php echo $config['media']['videointern'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.h264.mp4'; ?>" type='video/mp4'>
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
			<p><?php echo htmlspecialchars( $this->metadata['KURZBESCHRIEB'] ); ?></p>
		</div>
	<?php if( $this->doc->embedded ) {	?>
<img style="margin: 5px;" src="<?php echo $config['media']['picopen'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00005.thumb.png'; ?>" />
<img style="margin: 5px;" src="<?php echo $config['media']['picopen'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00009.thumb.png'; ?>" />
<img style="margin: 5px;" src="<?php echo $config['media']['picopen'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00013.thumb.png'; ?>" />
<img style="margin: 5px;" src="<?php echo $config['media']['picopen'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00017.thumb.png'; ?>" />
<img style="margin: 5px;" src="<?php echo $config['media']['picopen'].'/VWW'.intval($this->metadata['Publikationsnummer']).'.00021.thumb.png'; ?>" />
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
									<a href="javascript:doSearchFull('', '', [], {source: ['Wenkenpark'], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
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
			function initWenkenpark() {
				
			}
		</script>

<?php

//		echo "<code>".nl2br(str_replace( ' ', '&nbsp;', print_r( $this->metadata, true )))	."</code>\n";
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
					<?php if( @strlen(trim( $this->metadata['AutorinVN2'])))
						echo trim( $this->metadata['AutorinVN2']).' '.trim( $this->metadata['AutorInN2'])."<br />\n"; ?>
					<?php if( @strlen(trim( $this->metadata['AutorinVN3'])))
						echo trim( $this->metadata['AutorinVN3']).' '.trim( $this->metadata['AutorInN3'])."<br />\n"; ?>
<!--
					<?php if( @strlen(trim( $this->metadata['KonzeptDrehbuchVor1'])))
						echo "Konzept/Drehbuch: ".trim( $this->metadata['KonzeptDrehbuchVor1']).' '.trim( $this->metadata['KonzeptDrehbuchNach1'])."<br />\n"; ?>
					<?php if( @strlen(trim( $this->metadata['KonzeptDrehbuchVor2'])))
						echo "Konzept/Drehbuch: ".trim( $this->metadata['KonzeptDrehbuchVor2']).' '.trim( $this->metadata['KonzeptDrehbuchNach2'])."<br />\n"; ?>
					<?php if( @strlen(trim( $this->metadata['KAMERA'])))
						echo "Kamera: ".trim( $this->metadata['KAMERA'])."<br />\n"; ?>
					<?php if( @strlen(trim( $this->metadata['Schnitt'])))
						echo "Schnitt: ".trim( $this->metadata['Schnitt'])."<br />\n"; ?>
					<?php if( @strlen(trim( $this->metadata['MUSIK'])))
						echo "Musik: ".trim( $this->metadata['MUSIK'])."<br />\n"; ?>
					<?php if( @strlen(trim( $this->metadata['Ton'])))
						echo "Ton: ".trim( $this->metadata['Ton'])."<br />\n"; ?>
-->
                    <?php if( strlen( $this->metadata['Produktionsjahr'] )) echo 'Jahr: '.htmlspecialchars( $this->metadata['Produktionsjahr'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['LAENGE'] )) echo 'Dauer: '.htmlspecialchars( $this->metadata['LAENGE'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Publikationsnummer'] )) echo 'Publikationsnummer: '.htmlspecialchars( $this->metadata['Publikationsnummer'] )."<br />\n"; ?>
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