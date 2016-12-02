
var searcharea = "all";
function facetSearch( q, facet, value, add ) {
	if( add ) 
	{
		if( !( facet in q.facets ))
			q.facets[facet] = [];
		q.facets[facet].push( value );
	}
	else {
		var index = q.facets[facet].indexOf( value );
		if( index > -1 )
			q.facets[facet].splice( index, 1 );
	}
}

function initSearch( area, pagesize ) {
	if ( !pagesize ) {
        pagesize = 12;
    }
    searcharea = area;
	$('.search-panel .dropdown-menu').find('a').click(function(e) {
		   e.preventDefault();
		   var param = $(this).attr("href").replace("#","");
		   var concept = $(this).text();
		   $('.search-panel button#search_concept').html(concept+" <i class=\"fa fa-caret-down\" aria-hidden=\"true\"></i>");
		   $('.input-group #search_param').val(param);
		   window.location.hash = param;
           searcharea = param;
	   });
	
	$('#searchbutton').click( function(e) {
	   doSearch( $('#searchtext').val(), 0, pagesize );
	});
	
	 $('#searchtext').keypress(function (e) {
		if (e.which == 13) {
		  doSearch( $('#searchtext').val(), 0, pagesize );
		  return false;    //<---- Add this line
		}
	 });

	$('#searchbutton0').click( function(e) {
	   doSearch( $('#searchtext0').val(), 0, pagesize );
	});
	
	 $('#searchtext0').keypress(function (e) {
		if (e.which == 13) {
		  doSearch( $('#searchtext0').val(), 0, pagesize );
		  return false;    //<---- Add this line
		}
	 });
     
	 $('input.facet').change(function() {
		doSearch( $('#searchtext').val(), 0, pagesize ); 
	 });
	 
	var param = window.location.hash.replace("#","");
	if ( param.length < 1 ) {
	   param = searcharea;
	}
    searcharea = param;
	var concept = $('a[href="#'+param+'"]').text();
	if ( concept.length > 1 ) {
	   $('.search-panel button#search_concept').html(concept+" <i class=\"fa fa-caret-down\" aria-hidden=\"true\"></i>");
	   $('.input-group #search_param').val(param);
	}

}

function navbarSearch(page, pagesize) {
	searchtext = $('#searchtext').val();
	//searcharea = window.location.hash.replace("#","");
	
	var q = {
		query: searchtext,
		area: searcharea,
		filter: [],
		facets: {},
	}
	
	$("input.facet").each(function() {
		if( $(this).prop("checked") )
		{
			facet = $(this).attr("id");
			if(!( facet in q.facets )) {
				q.facets[facet] = [];
			}
			q.facets[facet].push($(this).val())
		}
	});
	
	var json = JSON.stringify( q );
	$('#searchjson').val( json );
    
    var md5sum = md5( json );
    var plist = window.location.pathname.split( '/' );
    plist.pop();
    var pathname = plist.join( '/');
	var url = window.location.origin + pathname + '/search.php?q='+encodeURIComponent( md5sum )+'&page='+page+'&pagesize='+pagesize;
	
	$('#searchform').attr('action', url).submit();
}

function doSearchFull(query, area, filter, facets, page, pagesize ) {
	
	if( typeof facets.source == 'undefined' ) {
		facets["source"] = ["NEBIS"];
	}
	
	var q = {
		query: query,
		area: area,
		filter: filter,
		facets: facets,
	}

	var json = JSON.stringify( q );

	$.post( 'query.load.php', {query: json}, function(md5sum) {
			//alert(md5sum);
			if( md5sum == null ) {
				alert( "invalid query" );
				return;
			}
			var plist = window.location.pathname.split( '/' );
			plist.pop();
			var pathname = plist.join( '/');
			var url = window.location.origin + pathname + '/search.php?q='+encodeURIComponent( md5sum )+'&page='+page+'&pagesize='+pagesize;
			window.location.href = url;
		}
	);

	return;
		
	
	$('#searchjson').val( json );
    
    var md5sum = md5( json );
    var plist = window.location.pathname.split( '/' );
    plist.pop();
    var pathname = plist.join( '/');
	var url = window.location.origin + pathname + '/search.php?q='+encodeURIComponent( md5sum )+'&page='+page+'&pagesize='+pagesize;
	
	$('#searchform').attr('action', url).submit();
}

function doSearch( searchtext, page, pagesize) {
	
	if ( typeof doSearch.running == 'undefined' ) {
        // It has not... perform the initialization
        doSearch.running = true;
    }
	else return;
//	searchtext = $('#searchtext').val();
	//searcharea = window.location.hash.replace("#","");

	var facets = {};
	
	$("input.facet").each(function() {
		if( $(this).prop("checked") )
		{
			facet = $(this).attr("id");
			if(!( facet in facets )) {
				facets[facet] = [];
			}
			facets[facet].push($(this).val())
		}
	});
	
	// categories
	var selected = [];
	var slist = $('#categorytree').jstree().get_selected(true);
	// alle ausgewählten in liste
	for (var obj in slist) {
		selected.push( slist[obj].id );
	}
	// jetzt nur die ausgewählten in die liste, deren parent nicht schon in der liste ist
	for (var obj in slist) {
		var o = slist[obj];
		if ( $.inArray( o.parent, selected ) != -1 ) {
            continue;
        }
		if(!( 'category' in facets )) {
			facets['category'] = [];
		}
		facets['category'].push(slist[obj].id)
	}
	

	doSearchFull( searchtext, searcharea, [], facets, page, pagesize );
}

function pageSearch( md5, page, pagesize ) {
    var plist = window.location.pathname.split( '/' );
    plist.pop();
    var pathname = plist.join( '/');
	var url = window.location.origin + pathname + '/search.php?q='+encodeURIComponent( md5 )+'&page='+page+'&pagesize='+pagesize;
	window.location.href=url;
}

var mediathek = null;
var mediathek3D = null;
var hash = null;
var px = 0;
var py = -5;
var pz = 5;
var gridWidth = 500;

function init3D( boxes, camJSON ) {
	
	mediathek = new Mediathek( {
		camType: "perspective",
		renderDOM: $(".renderer"),
		backgroundColor: 0x425164,
		camJSON: null, //'[0.9993451833724976,0.015195777639746666,-0.03283816948533058,0,0.01806975156068802,0.5766857266426086,0.8167662024497986,0,0.03134870156645775,-0.816824734210968,0.5760335326194763,0,286.9632568359375,-7477.142578125,5272.96044921875,1]',
		doWindowResize: true,
		//controltype: 'fly',
	})
	
	mediathek3D = new Mediathek3D( {
			floorImage: 'img/mt_background.png',
//			satImage: 'baselcard.jpg',
			boxData: 'kistendata.load.php',
			bottomColor: 0x283744,
			boxColor: 0xe0e0e0,
			//boxColorHighlight: 0x283744,
			boxDelay: 1,
			drawOnly: boxes,
		}, 
		function( object ) {
			mediathek.scene.add(object);
			object.boxHighlight( hash, true );
			mediathek.animate();
			
			object.renderBoxes(0);

			for( var i = 0; i < boxes.length; i++ ) {
				box = boxes[i];
				if( box.length >= 4 ) {
					mediathek3D.boxHighlight( box.substring( 0, 4), true );
				}
			}
			if( camJSON == null ) {
				mediathek.camera.position.set( px*gridWidth, py*gridWidth, pz*gridWidth );
				mediathek.camera.up = new THREE.Vector3(0,0,1);
				b = mediathek3D.boxes[box.substring( 0, 4)];
				if( b ) mediathek.controls.target.copy( b.position );
			}
			else {
				mediathek.setCamJSON( camJSON );
			}
			$(document).keyup( function( event ) {
				return;
				console.log( event.which );
				if ( mediathek == null ) {
                    return;
                }
				var set = false;
				switch ( event.which ) {
                    case 33: // PgUp
						pz++;
						set = true;
						break;
                    case 34: // PgDown
						pz--;
						set = true;
						break;
                    case 39: // ->
						px++;
						set = true;
						break;
                    case 37: // <-
						px--;
						set = true;
						break;
                    case 38: // up
						py++;
						set = true;
						break;
                    case 40: // down
						py--;
						set = true;
						break;
					case 80: // p
						px = Math.round(mediathek.camera.position.x / gridWidth);
						py = Math.round(mediathek.camera.position.y / gridWidth);
						pz = Math.round(mediathek.camera.position.z / gridWidth);
						set = true;
						break;
                }
				if ( set ) {
					mediathek.camera.position.set( px*gridWidth, py*gridWidth, pz*gridWidth );
					mediathek.camera.up = new THREE.Vector3(0,0,1);
					box = mediathek3D.boxes[hash.substring(0, 4)];
					mediathek.controls.target.copy( box.position );				
                }
				
			});
		}
	);
}

function  initBoxes(c) {
	
}
