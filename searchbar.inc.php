<?php
function searchbar( $query, $area ) {
	$areas = array( 'oa'=>'Open Access',
					'ebooks'=>'E-Books',
					);
	
    ob_start();
?>
			<div class="input-group">
				  <input type="text" id="searchtext" class="form-control" value="<?php echo htmlspecialchars( trim( $query ) == '*' ? '' : $query ); ?>" />
				  <span class="input-group-btn">
					<button class="" id="searchbutton" type="button">
						<i class="fa fa-search" aria-hidden="true"></i>
					</button>
				  </span>
			</div>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
?>