<?php
function mediathekheader( $current, $area ) {
    ob_start();
?>
<!DOCTYPE html>
<html class="full" lang="de">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="description" content="HGK Mediathek am Campus der Künste">
	<meta name="keywords" content="art, book, media, video, audio">
	<meta name="author" content="Tabea Lurk and Jürgen Enge">

	<title>
	  
		Welcome &middot; Mediathek der Künste
	  
	</title>

	<!-- Bootstrap core CSS -->

   <link href="css/bootstrap.min.css" rel="stylesheet">
   <link href="css/bootstrap.min.css" rel="stylesheet">
   <link href="css/tether.min.css" rel="stylesheet">
   <link href="css/tether-theme-basic.min.css" rel="stylesheet">
   <link href="css/mediathek.css" rel="stylesheet">
   
<?php if( $current == 'detail' ) { ?>	
   <link href="css/video-js.css" rel="stylesheet">
<?php } ?>

   <style type="text/css">
<?php if( $current == '' ) { ?>	
		body {
			margin-top: 0px;
		}
		
		.full {
			background: url('img/mt_full.jpg') no-repeat center center fixed;
			background-color: transparent;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			background-size: cover;
			-o-background-size: cover;
		}
<?php } ?>
   </style>
   
</head>
<body>
<form style="visibility: hidden;" id="searchform" action="#" method="post">
	<input type="hidden" id="searchjson" name="query" value="" />
</form>

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

    $html = ob_get_contents().navbar($current, $area);
    ob_end_clean();
    return $html;
}
?>