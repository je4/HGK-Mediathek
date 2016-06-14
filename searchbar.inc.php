<?php
function searchbar( $query, $area ) {
	$areas = array( 'oa'=>'Open Access',
					'ebooks'=>'E-Books',
					);
	
    ob_start();
?>
			<div class="input-group">
				  <div class="input-group-btn search-panel">
					  <button type="button" class="btn bw" id="search_concept" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo array_key_exists($area, $areas) ? htmlspecialchars( $areas[$area] ) : 'Search all'; ?> <i class="fa fa-caret-down" aria-hidden="true"></i>
					  </button>
					  <div class="dropdown-menu">
<!--
<?php
	foreach( $areas as $href=>$areatext) {
		echo "				  <a class=\"dropdown-item\" href=\"#{$href}\">".htmlspecialchars($areatext)."</a>\n";
	}
?>
						  <a class="dropdown-item" class="divider">
-->						  
						  <a class="dropdown-item" href="#all">Suche</a>
					  </div>
				  </div>
				  <input type="text" id="searchtext" class="form-control" value="<?php echo htmlspecialchars( $query ); ?>" />
				  <span class="input-group-btn">
					<button class="btn bw" id="search" type="button">
						<i class="fa fa-search" aria-hidden="true"></i>
					</button>
				  </span>
				  <form style="visibility: hidden;" id="searchform" action="#" method="post">
					<input type="hidden" id="searchjson" name="query" value="" />
				  </form>
			</div>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
?>