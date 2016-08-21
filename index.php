<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );


global $db;

$qobj = new \stdClass();
	$qobj->query = '';
	$qobj->area = 'all';
	$qobj->filter = array();
	$qobj->facets = array();

echo mediathekheader('home', 'Willkommen in der Mediathek der Künste', null);
?>

    <!-- home-page -->
    
    <div class="home-page">
        
        <!-- Introduction -->
        
        <div class="introduction">
            <!-- <div class="mask">
            </div> -->
            <div class="intro-content">
                <!-- <h1>HELLO<br>
                I'M <span>JOHN</span> DOE</h1> -->
                <h1 style="text-shadow: 2px 2px 3px rgba(255,255,255,0.8);">
                    Mediathek der
                </h1> 
                <span style="">Künste</span><span class="number"></span>
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
<!--
                <div class="social-media hidden-xs">
                    <a href="#" class="fa fa-facebook" data-toggle="tooltip" title="Facebook"></a>
                    <a href="#" class="fa fa-twitter" data-toggle="tooltip" title="Twitter"></a>
                    <a href="#" class="fa fa-plus" data-toggle="tooltip" title="Google+"></a>
                    <a href="#" class="fa fa-linkedin" data-toggle="tooltip" title="Linkedin"></a>
                    <a href="#" class="fa fa-behance" data-toggle="tooltip" title="Behance"></a>
                    <a href="#" class="fa fa-flickr" data-toggle="tooltip" title="Flicker"></a>
                    <a href="#" class="fa fa-instagram" data-toggle="tooltip" title="Instagram"></a>
                </div>
-->
            </div>
            
            <!-- Social Media Icons [ END ] -->
        </div>
        
        <!-- Navigation Menu -->
        
        <div class="menu">
            <div data-url_target="about" class="profile-btn menu_button">
                <img alt="" src="img/about400x400.png" style="width:100%; height:100%;">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-ios-people-outline hidden-xs"></i>
                    <h2>Über uns</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button -->
            
            <div data-url_target="search" class="portfolio-btn menu_button">
                <img alt="" src="img/search400x400.png">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-ios-search hidden-xs"></i>
                    <h2>Recherche</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
            <div data-url_target="news" class="news-btn menu_button">
                <img alt="" src="img/news400x400.png">
                <div class="mask" style="background: none;">
                </div>
                <div class="heading">
                    <i class="ion-ios-chatboxes-outline hidden-xs"></i>
                    <h2>News</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
            <div data-url_target="info" class="service-btn menu_button">
                <img alt="" src="img/info400x400.png">
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
		query: '<?php echo $qobj->query; ?>',
		area: '<?php echo $qobj->area; ?>',
		filter: [],
		facets: {},
	}

	

function init() {
	initSearch("");
/*
    var mapCanvas = document.getElementById('map-canvas');
    var mapOptions = {
        center: new google.maps.LatLng(40.565934, -122.388118),
        zoom: 16,
        scrollwheel: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(mapCanvas, mapOptions)

    var marker = new google.maps.Marker({ 
            position: new google.maps.LatLng(40.565234, -122.388118),
            title:"Boots4 Office"
        });

        // To add the marker to the map, call setMap();
        marker.setMap(map);

    //google.maps.event.addDomListener(window, 'load', initialize);

*/	
}
</script>   
	
    
    <script type="text/javascript" src="assets/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.mixitup.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.popup.min.js"></script>
    <script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script type="text/javascript" src="assets/js/contact.js"></script>
    <script type="text/javascript" src="assets/js/script.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script type="text/javascript" src="js/mediathek_helper.js"></script>
    <script type="text/javascript" src="js/mediathek_gui.js"></script>
	
	<script src="js/threejs/build/three.js"></script>
	<script src="js/threejs/build/TrackballControls.js"></script>
	<script src="js/threejs/build/OrbitControls.js"></script>
	<script src="js/threejs/build/CombinedCamera.js"></script>   
	<!-- script src="mediathek2.js"></script -->   
	<script src="js/mediathek.js"></script>   
	<script src="js/mediathek3d.js"></script>	


</body>
</html>