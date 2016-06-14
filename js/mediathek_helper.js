
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

function initSearch( area ) {
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
	
	$('#search').click( function(e) {
	   doSearch( 0, 12 );
	});
	
	 $('#searchtext').keypress(function (e) {
		if (e.which == 13) {
		  doSearch( 0, 12 );
		  return false;    //<---- Add this line
		}
	 });
     
	 $('input.facet').change(function() {
		doSearch( 0, 12 ); 
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


function doSearch(page, pagesize) {
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

function pageSearch( md5, page, pagesize ) {
    var plist = window.location.pathname.split( '/' );
    plist.pop();
    var pathname = plist.join( '/');
	var url = window.location.origin + pathname + '/search.php?q='+encodeURIComponent( md5 )+'&page='+page+'&pagesize='+pagesize;
	window.location.href=url;
}
