<?php
namespace Mediathek;

//require 'navbar.inc.php';
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

$sourcemap = array( 'NEBIS' => 'NEBIS-Bestand HGK',
				    'DOAJ' => 'Directory of Open Access Journals',
					'IKUVid' => 'Videosammlung Institut Kunst',
					'EZB' => 'Elektronische Zeitschriften Datenbank',
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
	<li><input type="text" class="setpage" value="<?php echo ($page+1); ?>" style="padding-right: 3px; text-align: right; width: 45px; background-color: transparent; border: 1px solid #004824;"> / <?php echo $numPages; ?></li>
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
	if( !$q ) {
		$qobj = new \stdClass();
		$qobj->facets = new \stdClass();
		$qobj->filter = new \stdClass();
		$qobj->area = '';
		$qobj->facets->source = array( 'NEBIS' );
		$qobj->query = '*';
		$query = json_encode( $qobj );
		$q = md5($query);
		writeQuery( $q, $qobj, $query );
	}
	else 
		$qobj = readQuery( $q );
}

if( $qobj->query == '' ) $qobj->query = '*';

// if( $qobj ) $session->storeQuery( md5( $qobj->query ));


echo mediathekheader('search', 'Mediathek - Suche - '.$qobj->query, $qobj->area);
?>
<div class="home-btn"><i class="ion-android-close"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings-strong': 'ion-log-in'; ?>"></i></div>

<div id="fullsearch" class="fullsearch-page container-fluid page">
    <div class="row">
            <!--( a ) Profile Page Fixed Image Portion -->

            <div class="image-container col-md-1 col-sm-12">
                <div class="mask" style="background-color: rgba(0, 0, 0, 0.15);">
                </div>
                <div style="left: 20%;" class="main-heading">
                    <h1>Suche</h1>
                </div>
            </div>

            <!--( b ) Profile Page Content -->

            <div class="content-container col-md-11 col-sm-12">
                <div class="clearfix">
                    <h2 class="small-heading">Mediathek der Künste</h2>

	<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
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
	$_q = "";
	foreach( $qobj->facets->source as $src ) {
		if( $_q != '' ) $_q .= ' OR ';
		if( $src == 'NEBIS' ) $_q .= ' (source:'.$helper->escapePhrase( $src ).' AND signature:'.$helper->escapePhrase('nebis:E75:*' ).')';
		else $_q .= ' (source:'.$helper->escapePhrase( $src ).')';
	}
    $squery->createFilterQuery('source')->addTag('source')->setQuery( $_q );	
}
if( @is_array( $qobj->facets->embedded )) {
    $squery->createFilterQuery('embedded')->addTag('embedded')->setQuery('embedded:('.implode(' ', $qobj->facets->embedded).')'	);	
}
if( @is_array( $qobj->facets->cluster )) {
	$_qstr = 'cluster_ss:(';
	$first = true;
	foreach( $qobj->facets->cluster as $clu ) {
		$_qstr .= ($first?'':' AND').' '.$helper->escapePhrase( $clu );
		$first = false;
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
$facetSetCluster->createFacetField('cluster')->setField('cluster_ss'); //->addExclude('cluster');
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
			<div class="clearfix full-height">
			  

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
		  </div>
   		  <div class="col-md-3" style="background-color: transparent;">
			<div style="">
			<span style="; font-weight: bold;">Kataloge</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetSource = $rs->getFacetSet()->getFacet('source');
				$i = 0;
				foreach ($facetSource as $value => $count) {
?>	
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="source" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->source ) && array_search($value, $qobj->facets->source) !== false ) echo " checked"; ?>>
							<label for="source<?php echo $i; ?>">
								<?php echo htmlspecialchars( $sourcemap[$value] ).' ('.$count.')'; ?>
							</label>
						</div>
<!--						
						<?php if( $i ) echo "<br />"; ?><input type="checkbox" id="source<?php echo $i; ?>" class="css-checkbox" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->source ) && array_search($value, $qobj->facets->source) !== false ) echo " checked"; ?>>
						<label for="source<?php echo $i; ?>" name="source<?php echo $i; ?>_lbl" class="css-label lite-x-green"><?php echo htmlspecialchars( $value ).' ('.$count.')'; ?></label>
-->						
<?php					
					$i++;
				}
?>
			</div>
			</div>

			<div style="">
			<span style="; font-weight: bold;">Eigener digitaler Bestand</span><br />
			<div class="facet" style="">
				<div class="marker"></div>
<?php
				$facetEmbedded = $rs->getFacetSet()->getFacet('embedded');
				$i = 0;
				foreach ($facetEmbedded as $value => $count) {
					//if( (@is_array( $qobj->facets->embedded ) && array_search($value, $qobj->facets->embedded) !== false ) === false && $count == 0 ) continue;
					if( $value == 'false' ) continue;
?>	
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="embedded" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->embedded ) && array_search($value, $qobj->facets->embedded) !== false ) echo " checked"; ?>>
							<label for="embedded<?php echo $i; ?>">
								<?php echo htmlspecialchars( 'direkt verfügbar' ).' ('.$count.')'; ?>
							</label>
						</div>
<!--						
						<?php if( $i ) echo "<br />"; ?><input type="checkbox" id="embedded<?php echo $i; ?>" class="css-checkbox" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->embedded ) && array_search($value, $qobj->facets->embedded) !== false ) echo " checked"; ?>>
						<label for="embedded<?php echo $i; ?>" name="embedded<?php echo $i; ?>_lbl" class="css-label lite-x-green"><?php echo htmlspecialchars( $value ).' ('.$count.')'; ?></label>
-->						
<?php				
					$i++;
				}

?>
			</div>
			</div>

			<div style="">
			<span style="; font-weight: bold;">Themen</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetCluster = $rs->getFacetSet()->getFacet('cluster');
				$i = 0;
				foreach ($facetCluster as $value => $count) {
					if( (@is_array( $qobj->facets->cluster ) && array_search($value, $qobj->facets->cluster) !== false ) === false && $count == 0 ) continue;
?>	
					<div class="checkbox checkbox-green">
						<input class="facet" type="checkbox" id="cluster" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->cluster ) && array_search($value, $qobj->facets->cluster) !== false ) { echo " checked"; } ?>>
						<label for="cluster<?php echo $i; ?>">
							<?php echo htmlspecialchars( $value ).' ('.$count.')'; ?>
						</label>
					</div>
<?php			
					$i++;
				}
//				var_dump( $facetCluster );
				if( isset( $qobj->facets->cluster ))
					foreach( $qobj->facets->cluster as $value ) {
//						echo "check: ".$value;
						if( array_key_exists( $value, $facetCluster->getValues() )) {
							continue;
						}
?>	
					<div class="checkbox checkbox-green">
						<input class="facet" type="checkbox" id="cluster" value="<?php echo htmlentities($value); ?>" checked >
						<label for="cluster<?php echo $i; ?>">
							<?php echo htmlspecialchars( $value ); ?>
						</label>
					</div>
<?php								
					}
?>
			</div>
			</div>
		  </div>

	   </div>
	</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Copyright &copy; 2016
							<a href="#">Mediathek der Künste</a>
						</p>
					</div>

					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="author">
							<a href="http://www.fhnw.ch/hgk" target="_blank">HGK FHNW</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
	
<?php
//include( 'bgimage.inc.php' );
?>
<script>

	var q = {
		query: '<?php echo $qobj->query; ?>',
		area: '<?php echo $qobj->area; ?>',
		filter: [],
		facets: {},
	}

	

function init() {

	 initSearch("<?php echo $qobj->area; ?>", <?php echo $session->getPageSize(); ?>);

	 $('.setpage').keypress(function (e) {
		if (e.which == 13) {
          var page = this.value;
		  pageSearch( '<?php echo $q; ?>', parseInt( page )-1, <?php echo $pagesize; ?> );
		  return false;    //<---- Add this line
		}
	 });

	$('body').on('click', '.setting-btn', function () {
		<?php if( $session->isLoggedIn()) { ?>
		window.location="settings.php";
		<?php } else { ?>
		window.location="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>";
		<?php } ?>
		
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

  
