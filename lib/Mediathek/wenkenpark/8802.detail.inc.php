<span style="; font-weight: bold;">Film abspielen</span><br>
<div class="facet" style="min-width: 550px; text-align: center;">
  <div class="marker" style=""></div>
  <video id="my-video" class="video-js" controls preload="auto" width="550"
  poster="<?php echo "{$basevideourl}{$this->metadata['media'][1]['mediums'][10]}"; ?>" data-setup="{}">
    <source src="<?php echo "{$basevideourl}{$this->metadata['media'][1]['video']}"; ?>" type='video/mp4'>
    <p class="vjs-no-js">
    To view this video please enable JavaScript, and consider upgrading to a web browser that
    <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video><br />
  <video id="audio1" class="video-js" controls preload="auto" height="0" width="550" data-setup="{}">
    <source src="<?php echo "{$basevideourl}"; ?>/open/8802/8802 Audiotracks/1.mp4" type='video/mp4'>
    <p class="vjs-no-js">
    To view this audio please enable JavaScript, and consider upgrading to a web browser that
    <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video><br />
  <video id="audio2" class="video-js" controls preload="auto" height="0" width="550" data-setup="{}">
    <source src="<?php echo "{$basevideourl}"; ?>/open/8802/8802 Audiotracks/2.mp4" type='video/mp4'>
    <p class="vjs-no-js">
    To view this audio please enable JavaScript, and consider upgrading to a web browser that
    <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video><br />
  <video id="audio3" class="video-js" controls preload="auto" height="0" width="550" data-setup="{}">
    <source src="<?php echo "{$basevideourl}"; ?>/open/8802/8802 Audiotracks/3.mp4" type='video/mp4'>
    <p class="vjs-no-js">
    To view this audio please enable JavaScript, and consider upgrading to a web browser that
    <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video><br />
  <video id="audio4" class="video-js" controls preload="auto" height="0" width="550" data-setup="{}">
    <source src="<?php echo "{$basevideourl}"; ?>/open/8802/8802 Audiotracks/4.mp4" type='video/mp4'>
    <p class="vjs-no-js">
    To view this audio please enable JavaScript, and consider upgrading to a web browser that
    <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video><br />
</div>
<script>
video = document.getElementById( "my-video" );
audio1 = document.getElementById( "audio1" );
audio2 = document.getElementById( "audio2" );
audio3 = document.getElementById( "audio3" );
audio4 = document.getElementById( "audio4" );
video.addEventListener('loadedmetadata', function() {
		setTimeout( sync, 2000 );
}, false);
video.addEventListener('play', function() {
  audio1.play();
  audio2.play();
  audio3.play();
  audio4.play();
	}, false);
	video.addEventListener('pause', function() {
    audio1.pause();
    audio2.pause();
    audio3.pause();
    audio4.pause();
	}, false);

function sync() {
  t = video.currentTime*1000;
  a1 = audio1.currentTime*1000;
  a2 = audio2.currentTime*1000;
  a3 = audio3.currentTime*1000;
  a4 = audio4.currentTime*1000;
  diff1 = t-a1;
  diff2 = t-a2;
  diff3 = t-a3;
  diff4 = t-a4;

  if( Math.abs( diff1 ) > 100 ) audio1.currentTime += diff1/1000;
  if( Math.abs( diff2 ) > 100 ) audio2.currentTime += diff2/1000;
  if( Math.abs( diff3 ) > 100 ) audio3.currentTime += diff3/1000;
  if( Math.abs( diff4 ) > 100 ) audio4.currentTime += diff4/1000;

  setTimeout( sync, 1000 );
}

</script>
