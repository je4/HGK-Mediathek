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

	var $entity;

    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

		$this->entity = new WenkenparkEntity( $db );
		$this->entity->loadFromDoc( $doc );
		$this->metadata = $this->entity->getData();
    }

    public function getHeading() {
    	$html = '';

    	ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
    	?>
    		                    <h2 class="small-heading">Videowochen im Wenkenpark</h2>

    					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
    <?php
            $html .= ob_get_contents();
            ob_end_clean();
    		return $html;
    	}

     public function getSchema() {
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array( 'Movie' );
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

		$entity = $this->entity;

		$basepicurl = $config['media']['videohybrid'].'/vww/';
		$basevideourl = $config['media']['videohybrid'].'/vww/';
		if( $this->metadata['Rechte Internet'] == 'station' ) $basevideourl = $config['media']['videohybridcert'].'/vww/';

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
//		if( !$loggedin || !$this->doc->embedded/* && !$cert */ ) {
if(( $session->isAdmin() || $session->inAnyGroup( $this->doc->acl_content )) && $this->doc->embedded ) {


//		if( $session->isAdmin() ) {
			$inc = dirname(__FILE__).'/wenkenpark/'.($this->metadata['Publikationsnummer']).'.detail.inc.php';
			//echo $inc;
			if( file_exists( $inc )) {
			 include( $inc );
			}
			elseif( isset( $this->metadata['media'][0]['video'] )) {
?>
	<span style="; font-weight: bold;">Film abspielen</span><br>
	<div class="facet" style="min-width: 550px; text-align: center;">
		<div class="marker" style=""></div>
		<video id="my-video" class="video-js" controls controlsList="nodownload" preload="auto" width="550"
		poster="<?php echo "{$basevideourl}{$this->metadata['media'][0]['mediums'][10]}"; ?>" data-setup="{}">
		  <source src="<?php echo "{$basevideourl}{$this->metadata['media'][0]['video']}"; ?>" type='video/mp4'>
		  <p class="vjs-no-js">
			To view this video please enable JavaScript, and consider upgrading to a web browser that
			<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		  </p>
		</video>

	</div>
<?php
			}
			elseif( isset( $this->metadata['media'][0]['images'] )) {
				?>
				<span style="; font-weight: bold;">Abbildungen</span><br>
				<div class="facet" style="min-width: 550px; text-align: center;">
				  <div class="marker" style=""></div>
				  <table style="width: 100%;">
				    <tr>
				      <td colspan="<?php echo count($this->metadata['media'][0]['images']); ?>" style="width: 80%;">
				        <img style="max-width: 100%; max-height: 100%;" id="mainimg" src="<?php echo $basevideourl.'/'.$this->metadata['media'][0]['images'][0]; ?>" />
				      </td>
				    </tr>
				    <tr>
							<?php foreach( $this->metadata['media'][0]['images'] as $key=>$val ) {
								$width = round(100/count($this->metadata['media'][0]['images']));
								?>
								<td style="width: <?php echo $width; ?>%; height: 100px; border: 1px solid white; background-image: url(<?php echo $basevideourl.'/'.$val; ?>); background-size: cover;">
					        <div style="width: 100%; height: 100%;" onclick="$('#mainimg').attr( 'src', '<?php echo $basevideourl.'/'.$val; ?>');">
					        </div>
					      </td>
							<?php } ?>
				    </tr>
				  </table>
				</div>
<?php
			}
			else {
			}
		}
		else
		{
?>
	<span style="font-weight: bold;">Hinweis</span><br>
	<div class="facet" style="min-width: 740px; text-align: center;">
		<div class="marker" style=""></div>
		<img src="<?php echo "{$basevideourl}{$this->metadata['media'][0]['mediums'][10]}"; ?>" /><br />
		Abspielen von Videos ist nicht für alle Nutzer freigeschaltet. <br />
		<?php
			switch( $this->metadata['Rechte Internet'] ) {
				case 'station':
					echo "Dieses Video kann in der Mediathek HGK auf einer Sichtungsstation angesehen werden.";
					break;
				case 'intern':
				echo "Dieses Video kann im HGK Netzwerk angesehen werden.";
				break;
			}
		?>
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
	<?php if( $this->doc->embedded && isset( $this->metadata['media'][0]['stills'] )) {
		for( $i = 0; $i < count( $this->metadata['media'][0]['stills'] ); $i++ ) {
				$img = "{$basepicurl}thumbs/{$this->metadata['Publikationsnummer']}.still.{$i}.thumb.png";
		?>
			<img style="margin: 5px;" src="<?php echo $img; ?>" />
<?php
		}
	} ?>
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
			</div>
		</div>
						<?php  }
						if( DEBUG ) {
							?>
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
			global $config;
		$html = '';
		$basepicurl = $config['media']['videohybrid'].'/vww/';

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

					<?php
					if( count( $this->metadata['media'][0]['stills'] ) > 0 ) {
						for( $i = 0; $i < min( 2, count( $this->metadata['media'][0]['stills'] )); $i++ ) {
							echo "<object type=\"image/png\" data=\"{$basepicurl}thumbs/{$this->metadata['Publikationsnummer']}.still.{$i}.thumb.png\"></object>\n";
						}
							echo "<br />\n";
					}
					?>
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
