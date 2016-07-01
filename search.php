<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );

global $db;

$topicmap = array( 
	'subject:personalname'=>'Personenname',
	'subject:corporatename'=>'Körperschaftsname',
	'subject:meetingname'=>'Kongresstitel',
	'subject:uniformtitle'=>'Einheitstitel',
	'subject:chronologicalterm'=>'Zeitschlagwort',
	'subject:topicalterm'=>'Sachschlagwort',
	'subject:geographicname'=>'Schlagwort nicht normiert',
	'subject:facetettopicalterm'=>'Sachschlagwort (kontrolliert)',
	'subject:hierarchicalplacename'=>'Ortsname',
	'subject:localsubjectaccess'=>'69X',
	'index:uncontrolled'=>'Index nicht normiert',
	'index:genreform'=>'Genre / Form',
	'index:occupation'=>'Beruf',
	'index:function'=>'Funktion',
	'index:curriculumobjective'=>'Lehrplanziel',	
);

$sourcemap = array( 'nebis' => 'HGK NEBIS-Bestand',
				    'doaj' => 'Directory of Open Access Journals',
					'ikuvid' => 'Videosammlung Institut Kunst',
				  );

// get request values
$query = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
$q = isset( $_REQUEST['q'] ) ? strtolower( trim( $_REQUEST['q'] )): null;
$page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 0;
$pagesize = isset( $_REQUEST['pagesize'] ) ? intval( $_REQUEST['pagesize'] ) : $session->getPageSize();
if( $page < 0 ) $page = 0;
$qobj = null;
$invalidQuery = false;

$urlparams = array( 'q'=>$q,
	'page'=>$page,
	'pagesize'=>$pagesize,
	);

function buildPagination() {
    global $type, $q, $pagesize, $page, $numPages;
    
?>
  <ul class="pager">
    <li class="<?php echo $page <= 0 ? "disabled" : ""; ?>"><a href="#" style="border: none; background-color: transparent;" onclick="<?php if( $page >0 ) echo "pageSearch( '{$q}', ".($page-1).", {$pagesize} );"; ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a></li>
	<li><input type="text" class="page" value="<?php echo ($page+1); ?>" style="width: 30px; background-color: transparent; border: 2px solid black;"> / <?php echo $numPages; ?></li>
    <li class="<?php echo $page >= $numPages-1 ? "disabled" : ""; ?>"><a href="#" style="border: none; background-color: transparent;" onclick="<?php if( $page < $numPages-1 ) echo "pageSearch( '{$q}', ".($page+1).", {$pagesize} );"; ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a></li>
  </ul>
</nav>
<?php
}

function writeQuery( $md5, $qobj, $json ) {
	global $db;
	
	$sql = "SELECT COUNT(*) FROM web_query WHERE queryid=".$db->qstr( $md5 );
	$num = intval( $db->GetOne( $sql ));
	if( $num > 0 ) return;
	
	$sql = "INSERT INTO web_query VALUES( ".$db->qstr( $md5 ).", ".$db->qstr( $qobj->query ). ", ".$db->qstr( $qobj->area ). ", ".$db->qstr( $json ).")";
	$db->Execute( $sql );
}

function readQuery( $md5 ) {
	global $db;
	
	$sql = "SELECT json FROM web_query WHERE queryid=".$db->qstr( $md5 );
	$json = $db->GetOne( $sql );
	if( $json ) return json_decode( $json );
	
	$qobj = new \stdClass();
	$qobj->query = '';
	$qobj->area = 'all';
	$qobj->filter = array();
	$qobj->facets = array();
	return $qobj;
}


if( $query ) {
	// query prüfen
	$qobj = json_decode( $query );
	$invalidQuery = !( property_exists( $qobj, 'query' )
					&& property_exists( $qobj, 'area' )
					&& property_exists( $qobj, 'filter' )
					&& property_exists( $qobj, 'facets' )
	   );

	if( !$invalidQuery ) {
		$md5 = md5($query);
		writeQuery( $md5, $qobj, $query );

		// md5-sum not valid for query
		if( $md5 != $q ) {
			header("Location: ?id={$md5}&page={$page}&pagesize={$pagesize}");
			exit;
		}
	}
	else {
		$qobj = null;
		$q = null;
		$query = null;
	}
}

if( !$qobj ) {
	$qobj = readQuery( $q );
}

if( $qobj->query == '' ) $qobj->query = '*';

// if( $qobj ) $session->storeQuery( md5( $qobj->query ));


echo mediathekheader('search', $qobj->area);
?>

	<div class="container-fluid" style="margin-top: 0px; padding: 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
<?php
?>
<?php

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( $pagesize );
$squery->setStart( $page * $pagesize );

if( $qobj->query != '*' ) {
	$qstr = Helper::buildSOLRQuery( $qobj->query );	
}
else
    $qstr = '*:*';
    
$acl_query = '';
foreach( $session->getGroups() as $grp ) {
	if( strlen( $acl_query ) > 0 ) $acl_query .= ' OR';
	$acl_query .= ' acl_meta:'.$helper->escapePhrase($grp);
}
$squery->createFilterQuery('acl_meta')->setQuery($acl_query);

switch( $qobj->area ) {
    case 'oa':
        $squery->createFilterQuery('openaccess')->setQuery('openaccess:true');
        break;
    case 'ebooks':
        $squery->createFilterQuery('ebooks')->setQuery('source:NEBIS AND online:true');
        break;
}

if( @is_array( $qobj->facets->source )) {
    $squery->createFilterQuery('source')->addTag('source')->setQuery('source:('.implode(' ', $qobj->facets->source).')'	);	
}
if( @is_array( $qobj->facets->embedded )) {
    $squery->createFilterQuery('embedded')->addTag('embedded')->setQuery('embedded:('.implode(' ', $qobj->facets->embedded).')'	);	
}
if( @is_array( $qobj->facets->cluster )) {
	$_qstr = 'cluster:(';
	foreach( $qobj->facets->cluster as $clu ) {
		$_qstr .= ' '.$helper->escapePhrase( $clu );
	}
	$_qstr .= ' )';
    $squery->createFilterQuery('cluster')->addTag('cluster')->setQuery($_qstr);
}
/*
if( @is_array( $qobj->facets->license )) {
	$_qstr = 'license:(';
	foreach( $qobj->facets->license as $lic ) {
		$_qstr .= ' '.$helper->escapePhrase( $lic );
	}
	$_qstr .= ' )';
    $squery->createFilterQuery('license')->addTag('license')->setQuery($_qstr);
}
*/
$squery->setQuery( $qstr );

$facetSetSource = $squery->getFacetSet();
$facetSetSource->createFacetField('source')->setField('source')->addExclude('source');
$facetSetEmbedded = $squery->getFacetSet();
$facetSetEmbedded->createFacetField('embedded')->setField('embedded')->addExclude('embedded');
//$facetSetLicense = $squery->getFacetSet();
//$facetSetLicense->createFacetField('license')->setField('license')->addExclude('license');
$facetSetCluster = $squery->getFacetSet();
$facetSetCluster->createFacetField('cluster')->setField('cluster')->addExclude('cluster');
//$facetSetAcl = $squery->getFacetSet();
//$facetSetAcl->createFacetField('acl')->setField('acl')->addExclude('acl');



$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
$numPages = floor( $numResults / $pagesize );
if( $numResults % $pagesize > 0 ) $numPages++;

echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".($page+1)." of {$numPages}) -->\n";

$res = new DesktopResult( $rs, $page * $pagesize, $pagesize, $db, $urlparams );


?>
		</div>
	   </div>
		
	   <div class="row">
		  <div class="col-md-9">
			  

<?php
	echo searchbar($qobj->query, $qobj->area );

	if( $numResults > 0 ) {
		buildPagination();
		echo $res->getResult();
		buildPagination();
	}
?>
			  </table>
		  </div>
   		  <div class="col-md-3" style="background-color: transparent;">
			<div style="">
			<span style="; font-weight: bold;">Katalog</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetSource = $rs->getFacetSet()->getFacet('source');
				foreach ($facetSource as $value => $count) {
?>	
				<div class="checkbox">
					<label>
						<input type="checkbox" class="facet" id="source" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->source ) && array_search($value, $qobj->facets->source) !== false ) echo " checked"; ?>>
						<?php echo $sourcemap[strtolower($value)].' ('.$count.')'; ?>
					</label>
				</div>				
<?php			
				}
?>
			</div>
			</div>

			<div style="">
			<span style="; font-weight: bold;">Eigene Inhalte</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetEmbedded = $rs->getFacetSet()->getFacet('embedded');
				foreach ($facetEmbedded as $value => $count) {
					if( $count == 0 ) continue;
?>	
				<div class="checkbox">
					<label>
						<input type="checkbox" class="facet" id="embedded" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->embedded ) && array_search($value, $qobj->facets->embedded) !== false ) echo " checked"; ?>>
						<?php echo htmlspecialchars( $value ).' ('.$count.')'; ?>
					</label>
				</div>	
<?php			
				}

?>
			</div>
			</div>

			<div style="">
			<span style="; font-weight: bold;">Keywords</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetCluster = $rs->getFacetSet()->getFacet('cluster');
				foreach ($facetCluster as $value => $count) {
					if( $count == 0 ) continue;
?>	
				<div class="checkbox">
					<label>
						<input type="checkbox" class="facet" id="cluster" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->cluster ) && array_search($value, $qobj->facets->cluster) !== false ) echo " checked"; ?>>
						<?php echo htmlspecialchars( $value ).' ('.$count.')'; ?>
					</label>
				</div>	
<?php			
				}
?>
			</div>
			</div>

<?php if( false ) { ?>		
			<div style="">
			<span style="; font-weight: bold;">Lizenz</span><br />
			<div class="bw" style="padding:5px;">
<?php
				$facetSource = $rs->getFacetSet()->getFacet('license');
				foreach ($facetSource as $value => $count) {
					if( $count == 0 ) continue;
?>	
				<div class="checkbox">
					<label>
						<input type="checkbox" class="facet" id="license" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->license ) && array_search($value, $qobj->facets->license) !== false ) echo " checked"; ?>>
						<?php echo htmlspecialchars( $value ).' ('.$count.')'; ?>
					</label>
				</div>				
<?php			
				}
?>
			</div>
			</div>
<?php } ?>			
		  </div>

	   </div>
	</div>

	
<?php
include( 'bgimage.inc.php' );
?>
<script>

	var q = {
		query: '<?php echo $qobj->query; ?>',
		area: '<?php echo $qobj->area; ?>',
		filter: [],
		facets: {},
	}

	

function init() {

	 initSearch("<?php echo $qobj->area; ?>");

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
	  body = modal.find('.modal-body');
	  body.empty();
	  body.append( '<div class="renderer"></div>')

	  renderer = modal.find( '.renderer' );
	  renderer.height( '400px');
	  width = body.width();
	  renderer.width( width );
	  init3D( kiste );
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
	
	 $('.page').keypress(function (e) {
		if (e.which == 13) {
          var page = this.value;
		  pageSearch( '<?php echo $q; ?>', parseInt( page )-1, <?php echo $pagesize; ?> );
		  return false;    //<---- Add this line
		}
	 });

	 
}
</script>   

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>   
<!-- script src="mediathek2.js"></script -->   
<script src="js/mediathek.js"></script>   
<script src="js/mediathek3d.js"></script>

<?php
echo mediathekfooter();
?>

  
