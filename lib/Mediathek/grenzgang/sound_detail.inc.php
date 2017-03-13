
<!-- START audio_detail.inc.php -->
				<div style="">
				<span style="; font-weight: bold;">Inhalt</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
	if( array_key_exists( 'meta:sonogram', $data ) && array_key_exists( 'meta:media', $data )) {
?>
		<center><canvas id="sonogram" width="400" height="150"></canvas><br />
		<!-- <img style="" src="<?php echo $data['meta:sonogram']; ?>"> -->
		<audio id="my-video" class="video-js" controls preload="auto" width="400" height="150"
		 data-setup="{     
			this.on('timeupdate', function () {
				console.log(this.currentTime());
			})
		}" poster="<?php echo $data['meta:sonogram']; ?>">
		  <source src="<?php echo $data['meta:media']; ?>" type='video/mp4'>
		  <p class="vjs-no-js">
			To view this video please enable JavaScript, and consider upgrading to a web browser that
			<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		  </p>
		</audio></center>
		<p><hr /></p>
<?php			
	}				
						
?>						
					</div>
				</div>
<?php if( array_key_exists( 'Beschreibung (1)', $data ) && strlen( trim( $data['Beschreibung (1)'])) > 0 ) { ?>				
				<div style="">				
				<span style="; font-weight: bold;">Abstract</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
						<?php echo nl2br( htmlspecialchars( $data['Beschreibung (1)'] )); ?>
					</div>
				</div>
<?php } ?>				
<!-- 
				<div style="">
				<span style="; font-weight: bold;">Technische Metadaten</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
							<table class="table">
<?php
		if( array_key_exists( 'meta:tika', $data )) 
		foreach( $data['meta:tika'] as $key=>$val ) {
			if( is_array( $val )) $val = implode( '; ', $val );
			echo "<tr>\n";
			echo "<td>".htmlentities( $key )."</td><td style=\"word-break: break-all;\">".htmlentities( substr( $val, 0, 255 )).(strlen( $val ) > 255 ? '...':'')."</td>\n";
			echo "</tr>\n";
		}
?>		
							</table>
					</div>
				</div>
-->				

		<script>
			function initGrenzgang() {
				
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
				imageObj.src = '<?php echo $data['meta:sonogram']; ?>';
	  
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