<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );


global $db;

$qobj = array();
	$qobj['query'] = '';
	$qobj['area'] = 'all';
	$qobj['filter'] = array();
	$qobj['facets'] = array();

echo mediathekheader('home', 'Willkommen in der Mediathek', null);
?>

    <!-- home-page -->

    <div class="home-page">

        <!-- Introduction -->

        <div class="introduction">

			<div id="fhnwtop" style="background-color: black; height: 40px; vertical-align: middle;">
				<img src="img/fhnwhgk_white_x35.png" style="left: 30px;">
			</div>

            <!-- <div class="mask">
            </div> -->
            <div class="intro-content">

                <h1 style="text-shadow: 2px 2px 3px rgba(255,255,255,0.8);">
                    Hochschule für Gestaltung und Kunst
                </h1>
                <span style="">Mediathek</span><span class="number"></span>
<!--                <p class="slogan-text text-capitalize" style=""><img style="position: static; top: auto; left: auto; width: 150px;" src="img/fhnw.png" /></p>
-->
				<p>
				<div class="input-group" style="width: 100%;">
					  <input type="text" id="searchtext0" class="form-control" value="">
					  <span class="input-group-btn">
						<button class="" id="searchbutton0" type="button">
							<i class="fa fa-search" aria-hidden="true"></i>
						</button>
					  </span>
				</div>
				</p>
            </div>

        </div>

        <!-- Navigation Menu -->

        <div class="menu">
            <div data-url_target="about" class="profile-btn menu_button">
                <img class="iimg" alt="" src="img/about400x400.png" style="width:100%; height:100%;">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-ios-people-outline hidden-xs"></i>
                    <h2>Über uns</h2>
                </div>
            </div>

            <!-- Single Navigation Menu Button -->

            <div data-url_target="search" class="portfolio-btn menu_button">
                <img class="iimg" alt="" src="img/search400x400.png">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-ios-search hidden-xs"></i>
                    <h2>Recherche</h2>
                </div>
            </div>

            <!-- Single Navigation Menu Button [ END ]  -->

            <div data-url_target="news" class="news-btn menu_button">
                <img class="iimg" alt="" src="img/news400x400.png">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-ios-chatboxes-outline hidden-xs"></i>
                    <h2>News</h2>
                </div>
            </div>

            <!-- Single Navigation Menu Button [ END ]  -->

            <div data-url_target="info" class="service-btn menu_button">
                <img class="iimg" alt="" src="img/info400x400.png">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-information hidden-xs"></i>
                    <h2>Info</h2>
                </div>
            </div>

            <!-- Single Navigation Menu Button [ END ]  -->

        </div>
    </div>

    <!--
    4 ) Close Button
    -->

    <div class="close-btn"><i class="ion-android-close"></i></div>


	<div id="content"></div>

<?php include( 'loader/search.load.php' ); ?>

    <!--
    9 ) Javascript
    - -->

<script>

	var q = {
		query: '<?php echo $qobj['query']; ?>',
		area: '<?php echo $qobj['area']; ?>',
		filter: [],
		facets: {},
	}



function init() {
	initSearch("");
}
</script>

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>
<!-- script src="mediathek2.js"></script -->
<script src="js/mediathek.js"></script>
<script src="js/mediathek3d.js"></script>
<script src="js/threex.domresize.js"></script>

<?php
echo mediathekfooter();

?>
<!--
    <script type="text/javascript" src="assets/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.mixitup.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.popup.min.js"></script>
    <script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="assets/js/contact.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script type="text/javascript" src="js/mediathek_helper.js"></script>
    <script type="text/javascript" src="js/mediathek_gui.js"></script>

	<script src="js/threejs/build/three.js"></script>
	<script src="js/threejs/build/TrackballControls.js"></script>
	<script src="js/threejs/build/OrbitControls.js"></script>
	<script src="js/threejs/build/CombinedCamera.js"></script>
	<script src="js/mediathek.js"></script>
	<script src="js/mediathek3d.js"></script>


</body>
</html>

-->
