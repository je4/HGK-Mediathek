
<!-- START 2d_detail.inc.php -->
<?php

?>
				<div style="">
				<span style="; font-weight: bold;">Inhalt</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
						<?php
							if( array_key_exists( 'meta:preview', $data )) {
						?>
								<center><img style="width: 100%;" src="<?php echo $data['meta:preview']; ?>" /></center>
								<p><hr /></p>
								<a href="<?php echo $data['meta:raw']; ?>" target="_blank">Download File</a>

						<?php
							}

						?>

					</div>
				</div>
		<script>
			function initGrenzgang() {


			}
		</script>
<!-- END 2d_detail.inc.php -->
