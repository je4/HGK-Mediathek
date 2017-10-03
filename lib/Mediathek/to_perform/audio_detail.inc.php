
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
	if( array_key_exists( 'mt:sonogram', $data ) && array_key_exists( 'mt:stream', $data )) {
?>
		<center><canvas id="sonogram" width="400" height="150"></canvas><br />
		<!-- <img style="" src="<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$dbid.'.svg.png'; ?>"> -->
		<audio id="my-video" class="video-js" controls preload="auto" width="400" height="150"
		 data-setup="{     
			this.on('timeupdate', function () {
				console.log(this.currentTime());
			})
		}" poster="<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$dbid.'.svg.png'; ?>">
		  <source src="<?php echo $config['media']['videointern'].'/to_perform/'.$dbid.'/'.$dbid.'.mp4'; ?>" type='video/mp4'>
		  <p class="vjs-no-js">
			To view this video please enable JavaScript, and consider upgrading to a web browser that
			<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		  </p>
		</audio></center>
		<p><hr /></p>
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