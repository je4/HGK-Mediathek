<!-- START text_detail.inc.php -->
<?php

?>

				<div style="">
				<span style="; font-weight: bold;">Inhalt</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
<?php
	if( @is_array( $data['meta:preview'] ) && count( $data['meta:preview'] ) ) {
		$pages = @intval( $data['meta:tika']['xmpTPg:NPages'] );
		if( $pages == 0 ) $pages = 1;
?>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
<?php for( $i = 0; $i < $pages; $i++ ) { ?>
    <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i; ?>"<?php echo $i == 0 ? " class=\"active\"" : ""; ?>></li>
<?php } ?>
  </ol>
  <div class="carousel-inner" role="listbox">
<?php for( $i = 0; $i < $pages; $i++ ) { ?>
    <div class="carousel-item<?php echo $i == 0 ? " active" : ""; ?>">
      <center><img src="<?php echo $data['meta:preview'][$i]; ?>" alt="Seite <?php echo ($i+1); ?>"></center>
    </div>
<?php } ?>
  </div>
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="icon-prev" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="icon-next" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

<?php
	}
	else {
		if( @strlen( $data['meta:fulltext'])) {
			echo nl2br( htmlspecialchars( $data['meta:fulltext'] ));
		}
	}
?>
<p><hr /></p>
	<a href="<?php echo $data['meta:raw']; ?>" target="_blank">Download File</a>

					</div>
				</div>

		<script>
			function initGrenzgang() {

			}
		</script>
<!-- END text_detail.inc.php -->
