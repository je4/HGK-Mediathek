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
//	var_dump( json_decode( $json ));
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
		$qobj->facets->source = $config['defaultsource'];
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
	$sourcefilterquery = "";
	foreach( $qobj->facets->source as $src ) {
		if( $sourcefilterquery != '' ) $sourcefilterquery .= ' OR ';
		if( $src == 'NEBIS' ) $sourcefilterquery .= ' (source:'.$helper->escapePhrase( $src ).' AND ( signature:'.$helper->escapePhrase('nebis:E75:*' ).' OR online:true ))';
		else $sourcefilterquery .= ' (source:'.$helper->escapePhrase( $src ).')';
	}
    $squery->createFilterQuery('source')->addTag('source')->setQuery( $sourcefilterquery );	
}
if( @is_array( $qobj->facets->category )) {
	$categoryfilterquery = "";
	foreach( $qobj->facets->category as $cat ) {
		if( $categoryfilterquery != '' ) $categoryfilterquery .= ' OR ';
		$categoryfilterquery .= ' (category:'.$helper->escapePhrase( $cat ).')';
	}
    $squery->createFilterQuery('category')->addTag('category')->setQuery( $categoryfilterquery );	
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
$facetSetCategory = $squery->getFacetSet();
$facetSetCategory->createFacetField('category')->setField('category')->addExclude('category');
$facetSetEmbedded = $squery->getFacetSet();
$facetSetEmbedded->createFacetField('embedded')->setField('embedded')->addExclude('embedded');
//$facetSetLicense = $squery->getFacetSet();
//$facetSetLicense->createFacetField('license')->setField('license')->addExclude('license');
$facetSetCluster = $squery->getFacetSet();
$facetSetCluster->createFacetField('cluster')->setField('cluster_ss'); //->addExclude('cluster');
//$facetSetAcl = $squery->getFacetSet();
//$facetSetAcl->createFacetField('acl')->setField('acl')->addExclude('acl');

$hl = $squery->getHighlighting();
$hl->setFields('abstract, content');
$hl->setSimplePrefix('<b class="highlight">');
$hl->setSimplePostfix('</b>');

$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
$numPages = floor( $numResults / $pagesize );
if( $numResults % $pagesize > 0 ) $numPages++;

echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".($page+1)." of {$numPages}) 
		Metafilter: {$acl_query} 
		Sourcefilter: {$sourcefilterquery}
-->\n";

$res = new DesktopResult( $rs, $page * $pagesize, $pagesize, $db, $urlparams );


?>
		</div>
	   </div>
		
	   <div class="row">
		  <div class="col-md-8">
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
   		  <div class="col-md-4" style="background-color: transparent;">
			<div style="">
			<span style="; font-weight: bold;">Kataloge</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetSource = $rs->getFacetSet()->getFacet('source');
				$i = 0;
				foreach ($facetSource as $value => $count) {
					if( !isset( $config['sourcemap'][$value] )) continue;
?>	
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="source" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->source ) && array_search($value, $qobj->facets->source) !== false ) echo " checked"; ?>>
							<label for="source<?php echo $i; ?>">
								<?php echo htmlspecialchars( $config['sourcemap'][$value] ).' ('.$count.')'; ?>
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

			<div style="overflow:hidden;">
			<span style="; font-weight: bold;">Kategorien</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<!--
<?php
				$facetCategory = $rs->getFacetSet()->getFacet('category');
				$i = 0;
				$categories = array();
				foreach ($facetCategory as $value => $count) {
					if( preg_match( '/^[0-9]+(!!.*)$/', $value, $matches )) {
						$categories[$matches[1]] = $count;
					}
					if( (@is_array( $qobj->facets->category )
							&& array_search($value, $qobj->facets->category) !== false ) === false
							&& $count == 0 )
						continue;
?>	
					<div class="checkbox checkbox-green">
						<input class="facet" type="checkbox" id="category" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj->facets->category ) && array_search($value, $qobj->facets->category) !== false ) { echo " checked"; } ?>>
						<label for="category<?php echo $i; ?>">
							<?php echo htmlspecialchars( str_replace( '!!', ':', $value )).' ('.$count.')'; ?>
						</label>
					</div>
<?php			
					$i++;
				}
				if( isset( $qobj->facets->category ))
					foreach( $qobj->facets->category as $value ) {
//						echo "check: ".$value;
						if( array_key_exists( $value, $facetCategory->getValues() )) {
							continue;
						}
?>	
					<div class="checkbox checkbox-green">
						<input class="facet" type="checkbox" id="category" value="<?php echo htmlentities($value); ?>" checked >
						<label for="category<?php echo $i; ?>">
							<?php echo htmlspecialchars( $value ); ?>
						</label>
					</div>
<?php								
					}
?>
-->
<?php								
				$cats = array_keys( $categories );
				sort( $cats );
				//var_dump( $cats );
				$len = 0;
?>
				<div id="categorytree">
<?php
	$tree = CategoryTree::buildTree( $facetCategory->getValues(), isset( $qobj->facets->category) ? $qobj->facets->category : array() );
//	echo nl2br( str_replace( ' ', '+', htmlentities( $tree->__toString() )));
	echo $tree->treeJS();
?>
<?php
/*
					foreach( $cats as $cat ) {
						
						if( strlen( $cat ) > $len ) {
							echo "	<ul>\n";
						}
						elseif( strlen( $cat ) < $len ) {
							echo "	</ul>\n";
						}
						else {
							if( preg_match( '/!!([^!]+)$/', $cat, $matches )) {
								echo "		<li>".htmlentities( $matches[1] )."</li>\n";								
							}
						}
					}
*/
?>

				</div>
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
	var inEvent = false;
	$('#categorytree').on('select_node.jstree', function (e, data) {
			if ( inEvent ) {
                return;
            }
			inEvent = true;
			console.log( "select node: %o", $('#categorytree').jstree().get_selected(true) );
			doSearch( $('#searchtext').val(), 0, <?php echo $session->getPageSize(); ?> );
			
	
		}).on('deselect_node.jstree', function (e, data) {
			function uncheckChildren( node ) {
				if( data.node.children.length > 0 ) {
					for( var id in data.node.children ) {
						data.instance.uncheck_node( data.node.children[id] );
					}
				}
			}
			function uncheckParents( node ) {
				if( data.node.parents.length > 0 ) {
					for( var id in data.node.parents ) {
						data.instance.uncheck_node( data.node.parents[id] );
					}
				}
			}

			if ( inEvent ) {
                return;
            }
			inEvent = true;
    
			console.log( "deselect node: %o", data );

			//uncheckChildren( data.node );
			//uncheckParents( data.node );
			
			setTimeout( function () { doSearch( $('#searchtext').val(), 0, <?php echo $session->getPageSize(); ?> ); }, 0 );
	
		}).jstree({ 
			"plugins" : [ "checkbox" ], 
			"core": { "themes":{ "icons":false  } }
		});

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
<script src="js/threex.domresize.js"></script>

<?php
echo mediathekfooter();
?>

  
