<!-- START text_detail.inc.php -->
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
						<b>Kategorie: </b><span style="word-break: break-all;"><?php echo htmlspecialchars( str_replace( ':', ' > ', $data['Categories'] )); ?></span><br />
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
		$pages = intval( $data['Pages'] );
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
      <center><img src="<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$dbid.'_preview-'.$i.'.png'; ?>" alt="Seite <?php echo ($i+1); ?>"></center>
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
		<p><hr /></p>
<?php			
	}										
?>					
	<a href="<?php echo $config['media']['picintern'].'/to_perform/'.$dbid.'/'.$data['mt:raw']; ?>" target="_blank">Download File</a>
	
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
		if( array_key_exists( 'mt:techmeta', $data )) 
		foreach( json_decode( $data['mt:techmeta'] ) as $key=>$val ) {
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

			}
		</script>			
<!-- END text_detail.inc.php -->