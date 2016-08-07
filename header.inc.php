<?php
function mediathekheader( $current, $title, $area ) {
    ob_start();
?>
<!doctype html>

<html lang="en" class="no-js">

<head>

    <!-- meta data -->

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=viewport content="width=device-width, initial-scale=1">
	<meta name="description" content="HGK Mediathek am Campus der Künste">
	<meta name="keywords" content="art, book, media, video, audio">
	<meta name="author" content="Tabea Lurk and Jürgen Enge">

    <!-- title and favicon -->

    <title><?php echo htmlspecialchars( $title ); ?></title>
    <link rel="icon" href="assets/img/icon/fav_icon.gif">
    

    <!--necessary stylesheets -->

    <link type="text/css" rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/ionicons.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/popup.css">
    <link type="text/css" rel="stylesheet" href="assets/css/owl.carousel.css">
    <link type="text/css" rel="stylesheet" href="assets/css/owl.theme.css">
    <link type="text/css" rel="stylesheet" href="assets/css/style.css">    
    
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<?php if( $current == 'detail' ) { ?>	
   <link href="css/video-js.css" rel="stylesheet">
<?php } ?>

    <link type="text/css" rel="stylesheet" href="css/mediathek.css">    
    <link type="text/css" rel="stylesheet" href="css/checkbox.css">    
    
    <!--[if IE]>
		<style>
		    .flip-container:hover .back,
			.flip-container.hover .back {
			    backface-visibility: visible !important;
			}
		</style>
	<![endif]-->


</head>


<body>
<?php if( $current == 'home' ) { ?>
	
    <!-- Preloader -->
    
    <div id="preloader">
        <div class="loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
<?php } ?>

	<!-- hidden search form for searchbar -->
	<form style="visibility: hidden;" id="searchform" action="#" method="post">
		<input type="hidden" id="searchjson" name="query" value="" />
	</form>

	<!-- general purpos modal dialog box -->
	<div class="modal fade" id="MTModal" tabindex="-1" role="dialog" aria-labelledby="MTModal" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			  <h4 class="modal-title" id="exampleModalLabel">Kiste</h4>
			</div>
			<div class="modal-body">
			</div>
		</div>
	  </div>
	</div>

<?php
//	echo "<!-- \n".print_r( $_SERVER, true )."\n-->\n";

    $html = ob_get_contents(); // .navbar($current, $area);
    ob_end_clean();
    return $html;
}
?>