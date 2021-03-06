<?php
function mediathekheader( $current, $title, $area, $csss = array(), $metas = array() ) {
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
<?php
foreach( $metas as $meta=>$val ) {
?>
    <meta name="<?php echo $meta; ?>" content="<?php echo $val; ?>">
<?php
}
 ?>
	

    <!-- title and favicon -->

    <title><?php echo htmlspecialchars( $title ); ?></title>
    <link rel="icon" href="assets/img/icon/fav_icon.gif">


    <!--necessary stylesheets -->
    <!-- link rel="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" type="text/css" / -->
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" type="text/css" />
    <link type="text/css" rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/ionicons.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/popup.css">
    <link type="text/css" rel="stylesheet" href="assets/css/owl.carousel.css">
    <link type="text/css" rel="stylesheet" href="assets/css/owl.theme.css">
    <link type="text/css" rel="stylesheet" href="assets/css/style.css">
    <link rel="text/css" href="/unica/css/stylesheet.css" type="text/css" />
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<?php if( $current == 'detail' ) { ?>
   <link href="css/video-js.css" rel="stylesheet">
<?php } ?>

    <link type="text/css" rel="stylesheet" href="css/mediathek.css">
    <link type="text/css" rel="stylesheet" href="css/checkbox.css">
    <link type="text/css" rel="stylesheet" href="css/bootstrap-editable.css">
	  <link rel="stylesheet" href="css/jstree_default/style.css" />
<?php
foreach( $csss as $css ) {
?>
    <link type="text/css" rel="stylesheet" href="<?php echo $css; ?>">
<?php
}
 ?>

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
<?php 
//	include_once("matomotracking.php") 
//	include_once("analyticstracking.php") 
?>
 
<div id="fhnwtop" style="background-color: black; height: 40px; vertical-align: middle; position: fixed; width: 100%;">
	<div style="float: left;">&nbsp;<img src="img/fhnwhgk_white_x35.png" style="left: 30px;"></div>
	<div style="color: white; vertical-align: middle; top: 0px; float: right;"><a style="color: white;" href=".">Mediathek</a>&nbsp;&nbsp;&nbsp;</div>
</div>
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
    <input type="hidden" name="area" value="<?php echo $area  ; ?>" />
	</form>

	<!-- general purpose modal dialog box -->
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
