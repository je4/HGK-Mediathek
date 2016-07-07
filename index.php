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
                <p class="slogan-text text-capitalize" style=""><img style="position: static; top: auto; left: auto; width: 350px;" src="img/fhnw.png" /></p>

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
                <img alt="" src="assets/img/about.jpg" style="width:100%; height:100%;">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-people-outline hidden-xs"></i>
                    <h2>Über uns</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button -->
            
            <div data-url_target="search" class="portfolio-btn menu_button">
                <img alt="" src="assets/img/portfolio.jpg">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-search hidden-xs"></i>
                    <h2>Suche</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
            <div data-url_target="contact" class="contact-btn menu_button">
                <img alt="" src="assets/img/contact.jpg">
                <div class="mask">
                </div>
                <div class="heading">
                    <i class="ion-ios-chatboxes-outline hidden-xs"></i>
                    <h2>News</h2>
                </div>
            </div>
            
            <!-- Single Navigation Menu Button [ END ]  -->
            
            <div data-url_target="info" class="service-btn menu_button">
                <img alt="" src="assets/img/service.jpg">
                <div class="mask">
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
    8 ) Contact Page
    -->
    
    <div id="contact" class="contact-page container-fluid page">
        <div class="row">
            <!--( a ) Contact Page Fixed Image Portion -->
            
            <div class="image-container col-md-3 col-sm-12">
                <div class="mask">
                </div>
                <div class="main-heading">
                    <h1>contact</h1>
                </div>
            </div>
            
            <!--( b ) Contact Page Content -->
            
            <div class="content-container col-md-9 col-sm-12">
                
                <!--( A ) Contact Form -->
                
                <div class="clearfix full-height">
                    <h2 class="small-heading">COME IN TOUCH</h2>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
                            <div class="contact-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="data">
                                            <i class="fa fa-map-marker"></i>
                                            <span>
                                                House -3, Road-2, Block - F, <br>
                                                 Akhalia Ghat R/A, Sylhet. <br>
                                            </span>
                                        </div>

                                        <div class="data">
                                            <i class="fa fa-envelope"></i>
                                            <span>
                                                hello@boots4.com
                                            </span>
                                        </div>

                                        <div class="data">
                                            <i class="fa fa-phone"></i>
                                            <span>
                                                +880 12345 - 67890
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="map-canvas"></div>
                                    </div>
                                </div>
                                    
                            </div>

                            <div class="row">
                                <form  id="contactForm" class="contact-form" method="post" action="php/contact.php">
                                    
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <input  name="name" type="text" class="form-control" id="name" required="required" placeholder="  Name">
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <input name="email" type="email" class="form-control" id="email" required="required" placeholder="  Email">
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <textarea name="massage" type="text" class="form-control" id="message" rows="5" required="required" placeholder="  Message"></textarea>
                                    </div>
                                    
                                    <div class="col-md-4 col-xs-12">
                                        <input type="submit" id="cfsubmit" class="btn btn-send" value="Say Hello">
                                    </div>
                                    <div id="contactFormResponse" class="col-md-8 col-xs-12"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ( D ) Footer -->
                
                <div class="footer clearfix">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <p class="copyright">Copyright &copy; 2015 
                                        <a href="#">Your Company</a>
                                    </p>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <p class="author">
                                        Theme by <a href="https://themewagon.com/" target="_blank">ThemeWagon</a>
                                    </p>
                                </div>
                            </div>      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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