
<!-- START audio_detail.inc.php -->
<?php
$picdir = '/data/www/html/pics/intern/to_perform/';
$viddir = '/data/www/html/video/intern/to_perform/';
$dbid = $data['Id'];
?>
			<div class="col-md-3">
				<div style="">
				<span style="; font-weight: bold;">Aktuelles Element</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
						<h5><?php echo htmlspecialchars( array_key_exists( 'Title', $data ) ? $data['Title'] : $data['Original Asset Name'] ); ?></h5>
						<?php if( array_key_exists( 'Author', $data )) { ?>
						<b>Author: </b><?php echo htmlspecialchars( $data['Author'] ); ?><br />
						<?php } ?>
						<?php if( array_key_exists( 'Mitwirkende/r', $data )) { ?>
						<b>Mitwirkende/r: </b><?php echo htmlspecialchars( $data['Mitwirkende/r'] 	); ?><br />
						<?php } ?>
						<?php if( array_key_exists( 'Description', $data )) { ?>
						<i><?php echo htmlspecialchars( $data['Description'] 	); ?></i><br />
						<?php } ?>
						<?php if( array_key_exists( 'Categories', $data )) { ?>
						<b>Kategorie: </b><span style="word-break: break-all;"><?php echo htmlspecialchars( str_replace( ':', '>', $data['Categories'] )); ?></span><br />
						<?php } ?>
						<?php if( array_key_exists( 'Author', $data )) { ?>
						<b>Coverage: </b><?php echo htmlspecialchars( $data['Coverage'] 	); ?><br />
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div style="">
				<span style="; font-weight: bold;">Inhalt</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
	if( array_key_exists( 'mt:preview', $data ) && $data['mt:preview'] == 'true' ) {
?>
		<center><img style="width: 100%;" src="<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$dbid.'_preview.png'; ?>" /></center>
		<p><hr /></p>
		<a href="<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$data['mt:raw']; ?>" target="_blank">Download File</a>
		
<?php			
	}				
						
?>						
							<table class="table">
<?php
		foreach( $data as $key=>$val ) {
			echo "<tr>\n";
			echo "<td>".htmlentities( $key )."</td><td style=\"word-break: break-all;\">".htmlentities( substr( $val, 0, 255 )).(strlen( $val ) > 255 ? '...':'')."</td>\n";
			echo "</tr>\n";
		}
		
?>		
							</table>
							<hr />
							<table class="table">
<?php
		if( array_key_exists( 'techmeta', $data )) 
		foreach( json_decode( $data['techmeta'] ) as $key=>$val ) {
			if( is_array( $val )) $val = implode( '; ', $val );
			echo "<tr>\n";
			echo "<td>".htmlentities( $key )."</td><td style=\"word-break: break-all;\">".htmlentities( substr( $val, 0, 255 )).(strlen( $val ) > 255 ? '...':'')."</td>\n";
			echo "</tr>\n";
		}
?>		
							</table>
					</div>
				</div>
			</div>
			<div class="col-md-3">
			</div>
		<script>
			function initto_perform() {
				
				var canvas = document.getElementById('sonogram');
				var context = canvas.getContext('2d');
				var imageObj = new Image();

				imageObj.onload = function() {
					context.drawImage(imageObj, 0, 0);
					context.beginPath();
					context.moveTo(0, 0);
					context.lineTo(0, 150);
					context.lineWidth = 2;
					context.strokeStyle =  '#4B5B6F'; //'#ff0000';
					context.stroke();
				};
				imageObj.src = '<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$dbid.'.svg.png'; ?>';
	  
				var vid = document.getElementById("my-video");
				vid.ontimeupdate = function() {
					var duration = this.duration;
					var current = this.currentTime; 
					var pos = current*400.0/duration;
					//console.log(current+"/"+duration+": "+pos );
					context.drawImage(imageObj, 0, 0);
					context.beginPath();
					context.moveTo(pos, 0);
					context.lineTo(pos, 150);
					context.lineWidth = 2;
					context.strokeStyle =  '#4B5B6F'; //'#ff0000';
					context.stroke();
	  
			};

			}
		</script>			
<!-- END audio_detail.inc.php -->