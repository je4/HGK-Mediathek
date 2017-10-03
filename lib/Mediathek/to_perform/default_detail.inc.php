<!-- START default_detail.inc.php -->
			<div class="col-md-9">
				<div style="">
				<span style="; font-weight: bold;">Aktuelles Element</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
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
<!-- END default_detail.inc.php -->