function homepageResponsive() {

	// Homepage Main Portions Responsive

	var windowsWidth = $(window).width(),
		windowsHeight = $(window).height();

	if (windowsWidth > 767) {

		$('.introduction , .menu').css({
			width: '50%',
			height: '100%'
		});

	} else {

		$('.introduction , .menu').css({
			width: '100%',
			height: '50%'
		});

	}

	// Homepage Profile Image Responsive

	var introWidth = $('.introduction').width(),
		introHeight = $('.introduction').height(),
		bgImage = $('.introduction').find('img .bgimage'),
		menuBgImages = $('.menu > div img');

	if (introWidth > introHeight) {

		bgImage.css({
			width: '100%',
			height: 'auto'
		});
		menuBgImages.css({
			width: '100%',
			height: 'auto'
		});

	} else {

		bgImage.css({
			width: 'auto',
			height: '100%'
		});
		menuBgImages.css({
			width: 'auto',
			height: '100%'
		});

	}

}

function hideBoots4Menu()
{
	var introWidth = $('.introduction').width(),
		menuWidth = $('.menu').width();

	$('.introduction').animate({
		left: '-' + introWidth
	}, 1000, 'easeOutQuart');
	$('.menu').animate({
		left: menuWidth
	}, 1000, 'easeOutQuart', function () {
		$('.home-page').css({
			visibility: 'hidden'
		});
	});
}

// Hide Menu
$('.menu').on('click', '.menu_button' , function () {
	// for div.menu_button there's another event
	if( !$(this).is('div') )
		hideBoots4Menu();
});

// Show Reletive Page Onclick

$('.menu').on('click', 'div.menu_button' , function(){
	var selectedPage = $(this).data('url_target');
	if ( !($('#'+selectedPage).length) ) {
		// if target does not exists load it...
		$('#content').load( 'loader/'+selectedPage+'.load.php', function( response, status, xhr ) {
			if ( status == "error" ) {
			  var msg = "Sorry but there was an error while loading 'loader/"+selectedPage+".load.php': ";
			  alert( msg + xhr.status + " " + xhr.statusText );
			}
			else {
				// ...and show it
				hideBoots4Menu();
				window.location.hash = selectedPage;
				$('#'+selectedPage).fadeIn(1200);
				$(window).scrollTop(0);
			}
		});
	}
	else {
		// target exists, just show it
		hideBoots4Menu();
		window.location.hash = selectedPage;
		$('#'+selectedPage).fadeIn(1200);
		$(window).scrollTop(0);
	}
});

// Close Button, Hide Menu

$('body').on('click', '.close-btn', function () {
	window.location.hash="";
	$('.home-page').css({
		visibility: 'visible'
	});
	$('.introduction, .menu').animate({
		left: 0
	}, 1000, 'easeOutQuart');
	$('.page').fadeOut(800);
	removeHash ();
	$(window).scrollTop(0);
	$('#content').empty();
});

$('body').on('click', '.home-btn', function () {
	window.location="index.php";
});

	
function removeHash () {
	history.pushState("", document.title, window.location.pathname
		+ window.location.search);
}


/*  ------------------
    Remove Preloader
    ------------------  */

$(window).load(function () {
    $('#preloader').delay(350).fadeOut('slow', function () {
        //$('.profile-page, .portfolio-page, .service-page, .contact-page').hide();
    });
});

$(document).ready(function () {

    'use strict';


        // Homepage Profile Image Responsive

        var introWidth = $('.introduction').width(),
            introHeight = $('.introduction').height(),
            bgImage = $('.introduction').find('img'),
            menuBgImages = $('.menu > div img');

        if (introWidth > introHeight) {

            bgImage.css({
                width: '100%',
                height: 'auto'
            });
            menuBgImages.css({
                width: '100%',
                height: 'auto'
            });

        } else {

            bgImage.css({
                width: 'auto',
                height: '100%'
            });
            menuBgImages.css({
                width: 'auto',
                height: '100%'
            });

        }

 

    $(window).on('load resize', homepageResponsive);

    $('.menu').on('click', 'div.profile-btn', function () {
        setTimeout(function(){
            $('.count').each(function () {
                $(this).prop('Counter',0).animate({
                    Counter: $(this).text()
                }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    }
                });
            });
        }, 100);
    });

    $('.menu').on('click', 'div.portfolio-btn', function () {
        setTimeout(function(){
            $('#projects').mixItUp();
        }, 100);
    });



    $('.menu').on('click','div.contact-btn', function () {
        setTimeout(function(){
            google.maps.event.trigger(map,'resize');
        },100);
    });

    /*  ----------------------------------------
         Tooltip Starter for Social Media Icons
        ----------------------------------------  */

    $('.intro-content .social-media [data-toggle="tooltip"]').tooltip({
        placement: 'bottom'
    });

    $('.contact-details .social-media [data-toggle="tooltip"]').tooltip();



    /*----------------------script for owl carousel sponsors---------------------*/

        $("#sponsor-list").owlCarousel({
                 
            autoPlay: 3000, //Set AutoPlay to 3 seconds
            stopOnHover: true,
            items : 3,
            itemsDesktop: [1200,3],
            itemsDesktopSmall: [991,3],
            itemsTablet: [767,2],
            itemsTabletSmall: [625,2],
            itemsMobile: [479,1]
        });


    /*  ----------------------------------------
         Kistentool
        ----------------------------------------  */
		$("#kiste").keyup(function() {
		   var text = $(this).val();
		   console.log( text );
		  if( text.length <= 2 ) { 
			$("#result").html("");
			return;
		  }
		$("#result").load('kistesig.load.php?signatur='+encodeURIComponent(text), function() {
		//                alert("loaded");
				});

		});

	$('#fullsearch').fadeIn(1200);
	
	$('#MTModal').on('shown.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var kiste = button.data('kiste') // Extract info from data-* attributes
		if ( typeof kiste == 'undefined' ) {
		  return;
		}
	  
	  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	  var modal = $(this)
	  modal.find('.modal-title').html('Regal <b>' + kiste.substring( 0, 1 ) + '</b> Kiste <b>' + kiste.substring( 1 ));
	  var body = modal.find('.modal-body');
	  body.empty();
	  body.append( '<div class="renderer"></div>')

	  var renderer = modal.find( '.renderer' );
	  renderer.height( '400px');
	  var width = body.width();
	  renderer.width( width );
	  init3D( kiste, null );
	})

	$('#MTModal').on('hidden.bs.modal', function (event) {
	  var modal = $(this)
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var kiste = button.data('kiste') // Extract info from data-* attributes
		if ( typeof kiste == 'undefined' ) {
		  return;
		}
	  mediathek.stopAnimate();
	  renderer = modal.find( '.renderer' );
	  renderer.empty();
	  mediathek = null;
	  mediathek3D = null;
	})
	
    /*  ----------------------------------------
         da suche
        ----------------------------------------  */
//		initSearch( '' );
 		
		
    /*  ----------------------------------------
         Google Suche
        ----------------------------------------  */
		$('#google').keypress(function(e) {
			if(e.which == 13) {
				  window.open('https://www.google.com?q='+encodeURIComponent($(this).val()), 'google', '');
				  return false;
			}
		});
		

    /*  -------------------------------
         PopUp ( for portfolio page )
        -------------------------------  */

    $(function () {
        $('.show-popup').popup({
            keepInlineChanges: true,
            speed: 500
        });
    });

    // location redirect to first load
    if(window.location.hash !== "" && window.location.hash) {
        var redirectPage = window.location.hash.slice(1);
        $('*[data-url_target="'+redirectPage+'"]').trigger('click');
    }

	init();
	

    // Show Reletive Page Onclick

    
});
