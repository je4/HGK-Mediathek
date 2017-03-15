
<!-- START audio_detail.inc.php -->
<?php
?>
				<div style="">
	<span style="; font-weight: bold;">Film abspielen</span><br>
	<div class="facet" style="min-width: 550px; text-align: center;">
		<div class="marker" style=""></div>
		<video id="my-video" class="video-js" controls preload="auto" width="550"
		poster="<?php echo $data['meta:shot'][0]; ?>" data-setup="{}">
		  <source src="<?php echo $data['meta:video']; ?>" type='video/mp4'>
		  <p class="vjs-no-js">
			To view this video please enable JavaScript, and consider upgrading to a web browser that
			<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
		  </p>
		</video>
		<p><hr /></p>
		<a href="<?php echo $data['meta:raw']; ?>" target="_blank">Download File</a>
	</div>
				</div>
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
