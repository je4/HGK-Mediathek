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

$debugstr = array();

// get request values
$search = isset( $_REQUEST['search'] ) ? $_REQUEST['search'] : null;
$facets = isset( $_REQUEST['facets'] ) ? $_REQUEST['facets'] : null;

$json = isset( $_REQUEST['json'] );
$query = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
$q = isset( $_REQUEST['q'] ) ? strtolower( trim( $_REQUEST['q'] )): null;
$page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 0;
$pagesize = isset( $_REQUEST['pagesize'] ) ? intval( $_REQUEST['pagesize'] ) : $session->getPageSize();
$pagesize = min( 2500, $pagesize );
if( $page < 0 ) $page = 0;
$qobj = null;
$invalidQuery = false;


$cachestr = '';
foreach( $session->getGroups() as $grp ) {
	$cachestr .= $grp;
}
$cachefile = md5($cachestr."-{$q}-{$page}").".dat";
$fullcachepath = "{$config['cachedir']}/{$cachefile{0}}/{$cachefile{1}}/search-{$q}-{$page}-{$cachefile}";

if( file_exists( $fullcachepath )) {
	$gzipoutput = file_get_contents( $fullcachepath );
	$headers = getallheaders();
	//var_dump( $headers );
	if( array_key_exists('Accept-Encoding', $headers)) {
		if( strpos($headers['Accept-Encoding'], 'gzip' ) !== false ) {
			header('Content-Encoding: gzip');
			header('Content-Length: '.strlen($gzipoutput));
			echo $gzipoutput;
			exit;
		}
	}
	echo gzdecode($gzipoutput);
	exit;
}

ob_start(); // segment 1
$pagestr = '';


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


if( $query ) {
	// query prüfen
	$qobj = json_decode( $query, true );
	
	
	$invalidQuery = !( array_key_exists( 'query', $qobj )
					&& array_key_exists( 'area', $qobj )
					&& array_key_exists( 'filter', $qobj )
					&& array_key_exists( 'facets', $qobj )
	   );
	if( !$invalidQuery ) {
	
	$query = $qobj['query'];
	$facets = $qobj['facets'];
	$catalogs = [];
	if( array_key_exists('catalog', $facets )) {
	   if( is_array($qobj['catalog'])) {
	      $catalogs = $qobj['catalog'];
	   } else {
	      $catalogs[] = $qobj['catalog'];
	   }
	}
	
	$str = "{$query}";
	foreach( $catalog as $cat ) {
	   $str += " catalog:\"{$cat}\"";
	}
	header("Location: https://mediathek.hgk.fhnw.ch/amp/search?searchtext=".urlencode($str));
	exit;

	
	$md5 = md5($query);
		Helper::writeQuery( $md5, $qobj, $query );

		// md5-sum not valid for query
		if( $md5 != $q ) {
			header("Location: ?q={$md5}&page={$page}&pagesize={$pagesize}".($json ? '&json':''));
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
		if( $search != "" ) {
			header("Location: https://mediathek.hgk.fhnw.ch/amp/search?searchtext=".urlencode($search));
			exit;
		}
		$qobj = array();
		$qobj['facets'] = array();
		$qobj['filter'] = array();
		$qobj['area'] = 'all';
		if( is_array( $facets )) $qobj['facets'] = $facets;
		else $qobj['facets']['catalog'] = $config['defaultcatalog'];
		$qobj['query'] = $search ? $search : '*';
		$query = json_encode( $qobj );
		$q = md5($query);
		Helper::writeQuery( $q, $qobj, $query );
		$session->storeQuery( $q );

		if( $search && false ) {
			var_dump( $qobj );
			exit;
		}

		header("Location: ?q={$q}&page={$page}&pagesize={$pagesize}".($json?'&json':''));
		exit;
	}
	else {
		$qobj = Helper::readQuery( $q );
		$session->storeQuery( $q );
		$sql = "SELECT config FROM web_query_config WHERE area=".$db->qstr( $qobj['area'] );
		$qconfig = $db->GetOne( $sql );
		//echo json_encode( $qobj );
	}
}

if( $qobj['query'] == '' ) $qobj['query'] = '*';
/*
	echo "<!-- ";
	var_dump( $qobj );
	echo " -->\n";
*/



$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( $pagesize );
$squery->setStart( $page * $pagesize );

if( $qobj['query'] != '*' ) {
	$qstr = Helper::buildSOLRQuery( $qobj['query'] );
}
else
    $qstr = '*:*';

$debugstr[] = "<!-- qstr: {$qstr} -->\n";

$acl_query = '';
foreach( $session->getGroups() as $grp ) {
	if( strlen( $acl_query ) > 0 ) $acl_query .= ' OR';
	$acl_query .= ' acl_meta:'.$helper->escapePhrase($grp);
}
$squery->createFilterQuery('acl_meta')->setQuery($acl_query);
$debugstr[] = "<!-- filter acl_meta: {$acl_query} -->\n";

$squery->createFilterQuery('nodelete')->setQuery('deleted:false');
$debugstr[] = "<!-- filter nodelete: deleted:false -->\n";

if( @is_array( $qobj['facets']['catalog'] )) {
	$catalogfilterquery = "";
	foreach( $qobj['facets']['catalog'] as $cat ) {
		if( $catalogfilterquery != '' ) $catalogfilterquery .= ' OR ';
		$catalogfilterquery .= ' (catalog:'.$helper->escapePhrase( $cat ).')';
	}
	$squery->createFilterQuery('catalog')->addTag('catalog')->setQuery( $catalogfilterquery );
	$debugstr[] = "<!-- filter catalog: {$catalogfilterquery} -->\n";
}

if( @is_array( $qobj['facets']['area'] )) {
	$areafilterquery = "";
	foreach( $qobj['facets']['area'] as $area ) {
		if( $areafilterquery != '' ) $areafilterquery .= ' OR ';
		$areafilterquery .= ' (category:'.$helper->escapePhrase( $area ).')';
	}
	$squery->createFilterQuery('area')->addTag('area')->setQuery( $areafilterquery );
	$debugstr[] = "<!-- filter $area: {$areafilterquery} -->\n";
}

if( DEBUG ) {
	if( @is_array( $qobj['facets']['category'] )) {
		$categoryfilterquery = "";
		foreach( $qobj['facets']['category'] as $cat ) {
			if( $categoryfilterquery != '' ) $categoryfilterquery .= ' OR ';
			$categoryfilterquery .= ' (category:'.$helper->escapePhrase( $cat ).')';
		}
	    $squery->createFilterQuery('category')->addTag('category')->setQuery( $categoryfilterquery );
	    $debugstr[] = "<!-- filter category: {$categoryfilterquery} -->\n";

	}
}
if( @is_array( $qobj['facets']['online'] )) {
	$onlinefilterquery = 'online:('.implode(' ', $qobj['facets']['online']).')';
	$squery->createFilterQuery('online')->addTag('online')->setQuery($onlinefilterquery);
	$debugstr[] = "<!-- filter online: {$onlinefilterquery} -->\n";

}

if( @is_array( $qobj['facets']['openaccess'] )) {
	$openaccessfilterquery = 'openaccess:('.implode(' ', $qobj['facets']['openaccess']).')';
	$squery->createFilterQuery('openaccess')->addTag('openaccess')->setQuery($openaccessfilterquery);
	$debugstr[] = "<!-- filter openaccess: {$openaccessfilterquery} -->\n";

}

/*
	if( @is_array( $qobj['facets']['embedded'] )) {
		$embeddedfilterquery = 'embedded:('.implode(' ', $qobj['facets']['embedded']).')';
		$squery->createFilterQuery('embedded')->addTag('embedded')->setQuery($embeddedfilterquery);
		$debugstr[] = "<!-- filter embedded: {$embeddedfilterquery} -->\n";

	}
*/
if( @is_array( $qobj['facets']['type'] )) {
	$typefilterquery = 'type:('.implode(' ', $qobj['facets']['type']).')';
	$squery->createFilterQuery('type')->addTag('type')->setQuery($typefilterquery);
	$debugstr[] = "<!-- filter type: {$typefilterquery} -->\n";

}
if( @is_array( $qobj['facets']['cluster'] )) {
	$_qstr = 'cluster_ss:(';
	$first = true;
	foreach( $qobj['facets']['cluster'] as $clu ) {
		$_qstr .= ($first?'':' AND').' '.$helper->escapePhrase( $clu );
		$first = false;
	}
	$_qstr .= ' )';
  $squery->createFilterQuery('cluster')->addTag('cluster')->setQuery($_qstr);
  $debugstr[] = "<!-- filter cluster: {$_qstr} -->\n";

}

if( true ) {
	$dismax = $squery->getDisMax();
	$dismax->setBoostQuery( $config['boost'] );
	$dismax->setQueryAlternative( $qstr );
	$squery->setQuery( '' );
} else {
	$squery->setQuery( $qstr );
}

$facetSetCatalog = $squery->getFacetSet();
$facetSetCatalog->createFacetField('catalog')->setField('catalog')->addExclude('catalog');

$facetSetSource = $squery->getFacetSet();
$facetSetSource->createFacetField('source')->setField('source')->addExclude('source');

if( @is_array( $qobj['facets']['license'] )) {
	$licensefilterquery = "";
	foreach( $qobj['facets']['license'] as $lic ) {
		if( $licensefilterquery != '' ) $licensefilterquery .= ' OR ';
		$licensefilterquery .= ' (license:'.$helper->escapePhrase( $lic ).')';
	}
	$squery->createFilterQuery('license')->addTag('license')->setQuery( $licensefilterquery );
	$debugstr[] = "<!-- filter license: {$licensefilterquery} -->\n";
}

$facetSetLicense = $squery->getFacetSet();
$facetSetLicense->createFacetField('license')->setField('license')->addExclude('license');

if( DEBUG ) {
	$facetSetCategory = $squery->getFacetSet();
	$facetSetCategory->createFacetField('category')->setField('category')->setLimit( $config['categorylimit'] )->addExclude('category');
}

// http://localhost/solr/base/select?facet.field={!key=area+facet.prefix=1!!area!!}category&facet.field={!key=field+facet.prefix=1!!field!!}category&facet=on&q=*:*&rows=0
$facetSetArea = $squery->getFacetSet();
$facetSetArea->createFacetField('area')->setField('category')->setPrefix( '1!!area' )->setLimit( 12 )->addExclude('area');
//$facetSetArea->createFacetQuery('area')->setQuery( 'category:1!!area!!*' )->addExclude('area');
//$facetSetArea->createFacetField("{!key=area+facet.prefix=1!!area!!}category");

$facetSetOnline = $squery->getFacetSet();
$facetSetOnline->createFacetField('online')->setField('online')->addExclude('online');
$facetSetOpenAccess = $squery->getFacetSet();
$facetSetOpenAccess->createFacetField('openaccess')->setField('openaccess')->addExclude('openaccess');

$facetSetType = $squery->getFacetSet();
$facetSetType->createFacetField('type')->setField('type')->addExclude('type');

$facetSetCluster = $squery->getFacetSet();
$facetSetCluster->createFacetField('cluster')->setField('cluster_ss')->setLimit( $config['clusterlimit'] ); //->addExclude('cluster');

$hl = $squery->getHighlighting();
$hl->setFields('abstract, content');
$hl->setSimplePrefix('<b class="highlight">');
$hl->setSimplePostfix('</b>');

//if( $qstr == '*:*' ) $squery->addSort('originalid', $query::SORT_DESC);

//$squery->setMethod(\Solarium\Core\Client\Request::METHOD_GET);

try {
	if( DEBUG ) {
		$squery->getDebug();
		$request = $solrclient->createRequest($squery);
		$debugstr[] = '<!-- Uri: ' . $request->getUri() . ' -->';
		$response = $solrclient->executeRequest($request);
		$rs = $solrclient->createResult($squery, $response);
		$debugResult = $rs->getDebug();
		$debugstr[] = '<!-- Querystring: ' . $debugResult->getQueryString() . ' -->';
		$debugstr[] = '<!-- Parsed query: ' . $debugResult->getParsedQuery() . ' -->';
		$debugstr[] = '<!-- Query parser: ' . $debugResult->getQueryParser() . ' -->';
		$debugstr[] = '<!-- Other query: ' . $debugResult->getOtherQuery() . ' -->';
	}
	else {
		$rs = $solrclient->select( $squery );
	}
}
catch ( \Exception $e ) {
	echo "Error: {$e}\n";
	echo $e->getTraceAsString();
	die();
}
$numResults = $rs->getNumFound();
$numPages = $json ? min( 100, floor( $numResults / $pagesize )) : floor( $numResults / $pagesize );
if( $numResults % $pagesize > 0 ) $numPages++;

$debugstr[] = "<!-- ".$qstr." (Documents: {$numResults} // Page ".($page+1)." of {$numPages}) -->\n";

if( $json ) {
	$data = array( 'pages'=>$numPages, 'numFound'=>$numResults, 'pageSize'=>$pagesize, 'pageNo'=>$page, $result = array() );
	$data['_self'] = $_SERVER['SCRIPT_URI'].'?q='.$q.'&pagesize='.$pagesize.'&page='.($page).'&json';
	if( $page+1 < $numPages) $data['_next'] = $_SERVER['SCRIPT_URI'].'?q='.$q.'&pagesize='.$pagesize.'&page='.($page+1).'&json';
	if( $page > 0 ) $data['_prev'] = $_SERVER['SCRIPT_URI'].'?q='.$q.'&pagesize='.$pagesize.'&page='.($page-1).'&json';

	foreach( $rs as $doc ) {
		$fields = (array)$doc->getFields();
		$fields['_self'] = substr( $_SERVER['SCRIPT_URI'], 0, -strlen( 'search.php' )).'detail.php?id='.$doc->id;
		$data['result'][] = $fields;
	}

	$data['facets'] = array();
	$facetSet = $rs->getFacetSet();
	foreach( $facetSet->getFacets() as $key=>$facet ) {
		$data['facets'][$key] = array();
		foreach( $facet as $range => $count ) {
			if( $count ) $data['facets'][$key][$range] = $count;
		}
	}

	header( 'Content-type: text/json' );
	echo json_encode( $data );
	exit;
}

echo mediathekheader('search', 'mediathek - Suche - '.$qobj['query'], $qobj['area'], array(), ['robots'=>'noindex'] );
?>
<div class="home-btn"><i class="ion-ios-close-circle-outline"></i></div>
<div class="setting-btn"><i class="<?php echo $session->isLoggedIn() ? 'ion-ios-settings': 'ion-ios-log-in'; ?>"></i></div>
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
                    <h2 class="small-heading">Mediathek</h2>

	<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
<?php
$pagestr .= ob_get_contents();
ob_flush();

foreach( $debugstr as $str ) echo $str."\n";

$res = new DesktopResult( $rs, $page * $pagesize, $pagesize, $db, $urlparams );

$pagestr .= ob_get_contents();
ob_flush();

?>
		</div>
	   </div>

	   <div class="row">
		  <div class="col-md-8">
			<div class="clearfix full-height">

<?php
	echo searchbar($qobj['query'], $qobj['area'] );

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
				$facetCatalog = $rs->getFacetSet()->getFacet('catalog');
				$i = 0;
				$catalog = array();
				foreach ($facetCatalog as $value => $count) {
					$catalog[$value] = $count;
				}

				foreach( $config['catalogmap'] as $cat=>$title ) {
					$value = $cat;
					$count = 0;
					if( array_key_exists( $cat, $catalog )) {
						$count = $catalog[$cat];
					}
					if( ( @is_array( $qobj['facets']['catalog'] ) && array_search($value, $qobj['facets']['catalog']) !== false ) || $count > 0 ) { ?>
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="catalog" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['catalog'] ) && array_search($value, $qobj['facets']['catalog']) !== false ) echo " checked"; ?>>
							<label for="catalog<?php echo $i; ?>">
								<?php echo htmlspecialchars( $title ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
							</label>
						</div>
<?php
					}
					$i++;
				}
?>
					<hr />
<?php
				foreach( $config['sourcemap'] as $cat=>$title ) {
					$value = $cat;
					$count = 0;
					if( array_key_exists( $cat, $catalog )) {
						$count = $catalog[$cat];
					}
						if( ( @is_array( $qobj['facets']['catalog'] ) && array_search($value, $qobj['facets']['catalog']) !== false ) || $count > 0 ) {
					?>
										<div class="checkbox checkbox-green">
											<input class="facet" type="checkbox" id="catalog" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['catalog'] ) && array_search($value, $qobj['facets']['catalog']) !== false ) echo " checked"; ?>>
											<label for="catalog<?php echo $i; ?>">
												<?php echo htmlspecialchars( $title ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
											</label>
										</div>
				<?php
						}
									$i++;
				}
				?>
			</div>
			</div>
<?php
$pagestr .= ob_get_contents();
ob_flush();

		if( DEBUG ) {

?>
			<div style="">
			<span style="; font-weight: bold;">Licenses</span><br />
			<div class="facet" style="">
				<div class="marker" style=""></div>
<?php
				$facetLicense = $rs->getFacetSet()->getFacet('license');
				$i = 0;
				if( $facetLicense ) foreach ($facetLicense as $value => $count) {
					if(!( @is_array( $qobj['facets']['license'] ) && array_search($value, $qobj['facets']['license']) !== false ) && $count == 0 ) continue;
?>
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="license" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['license'] ) && array_search($value, $qobj['facets']['license']) !== false ) echo " checked"; ?>>
							<label for="license<?php echo $i; ?>">
								<?php echo htmlspecialchars( $value ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
							</label>
						</div>
<?php
					$i++;
				}
?>
			</div>
			</div>
<?php
		}
?>
			<div style="">
			<span style="; font-weight: bold;">Zugriff</span><br />
			<div class="facet" style="">
				<div class="marker"></div>
<?php
				$facetOnline = $rs->getFacetSet()->getFacet('online');
				$i = 0;
				foreach ($facetOnline as $value => $count) {
					if( $value == 'false' ) continue;
					if(!( @is_array( $qobj['facets']['online'] ) && array_search($value, $qobj['facets']['online'] ) !== false ) && $count == 0 ) continue;
?>
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="online" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['online'] ) && array_search($value, $qobj['facets']['online'] ) !== false ) echo " checked"; ?>>
							<label for="online<?php echo $i; ?>">
								<?php echo htmlspecialchars( 'online verfügbar' ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
							</label>
						</div>
<?php
					$i++;
				}

				$facetOpenAccess = $rs->getFacetSet()->getFacet('openaccess');
				$i = 0;
				foreach ($facetOpenAccess as $value => $count) {
					if( $value == 'false' ) continue;
					if(!( @is_array( $qobj['facets']['openaccess'] ) && array_search($value, $qobj['facets']['openaccess'] ) !== false ) && $count == 0 ) continue;
					?>
											<div class="checkbox checkbox-green">
											<input class="facet" type="checkbox" id="openaccess" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['openaccess'] ) && array_search($value, $qobj['facets']['openaccess'] ) !== false ) echo " checked"; ?>>
											<label for="openaccess<?php echo $i; ?>">
												<?php echo htmlspecialchars( 'Open Access' ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
											</label>
										</div>
				<?php
					$i++;
				}
				?>
			</div>
			</div>

			<div style="">
			<span style="; font-weight: bold;">Bereich</span><br />
			<div class="facet" style="">
				<div class="marker"></div>
<?php
				$facetArea = $rs->getFacetSet()->getFacet('area');
				$i = 0;
				foreach ($facetArea as $value => $count) {
					if( $count == 0 ) continue;
					if( $value == '1!!area!!unknown' ) continue;
					//if( $value == 'false' ) continue;
/*
					if(!( @is_array( $qobj['facets']['area'] )
									&& array_search($value, $qobj['facets']['area'] ) !== false )
						&& $count == 0 ) continue;
*/
							list( $a, $b, $label ) = explode( '!!', $value );
?>
							<div class="checkbox checkbox-green">
								<input class="facet" type="checkbox" id="area" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['area'] ) && array_search($value, $qobj['facets']['area']) !== false ) { echo " checked"; } ?>>
								<label for="area<?php echo $i; ?>">
									<?php echo ( $label ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
								</label>
							</div>
<?php
					$i++;
					//if( $i == 10 ) break;
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
					if( (@is_array( $qobj['facets']['cluster'] ) && array_search($value, $qobj['facets']['cluster']) !== false ) === false && $count == 0 ) continue;
?>
					<div class="checkbox checkbox-green">
						<input class="facet" type="checkbox" id="cluster" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['cluster'] ) && array_search($value, $qobj['facets']['cluster']) !== false ) { echo " checked"; } ?>>
						<label for="cluster<?php echo $i; ?>">
							<?php echo ( $value ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
						</label>
					</div>
<?php
					$i++;
				}
//				var_dump( $facetCluster );
				if( isset( $qobj['facets']['cluster'] ))
					foreach( $qobj['facets']['cluster'] as $value ) {
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

			<div style="">
			<span style="; font-weight: bold;">Typen</span><br />
			<div class="facet" style="">
				<div class="marker"></div>
<?php
				$facetType = $rs->getFacetSet()->getFacet('type');
				$i = 0;
				foreach ($facetType as $value => $count) {
					if(!( @is_array( $qobj['facets']['type'] ) && array_search($value, $qobj['facets']['type']) !== false ) && $count == 0 ) continue;

					$t = strtolower( $value );

					$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];
?>
						<div class="checkbox checkbox-green">
							<input class="facet" type="checkbox" id="type" value="<?php echo htmlentities($value); ?>" <?php if( @is_array( $qobj['facets']['type'] ) && array_search($value, $qobj['facets']['type']) !== false ) echo " checked"; ?>>
							<label for="type echo $i; ?>">
								<i class="<?php echo $icon; ?>"></i> <?php echo htmlspecialchars( $value ).' ('.number_format( $count, 0, '.', "'" ).')'; ?>
							</label>
						</div>
<?php
					$i++;
				}

?>
			</div>
			</div>


<?php 
$pagestr .= ob_get_contents();
ob_flush();

	if( DEBUG ) { 
?>
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
					$i++;
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
	$tree = CategoryTree::buildTree( $facetCategory->getValues(), isset( $qobj['facets']['category']) ? $qobj['facets']['category'] : array() );
	echo $tree->treeJS();
?>

				</div>
			</div>
		</div>
<?php } /* DEBUG */ ?>


		  </div>

	   </div>
	</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Enjoy 2017
							<a href="#">Mediathek</a>
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
$pagestr .= ob_get_contents();
ob_flush();

//include( 'bgimage.inc.php' );
?>
<script>

	var q = {
		query: '<?php echo str_replace('\'', '\\\'', $qobj['query'] ); ?>',
		area: '<?php echo $qobj['area']; ?>',
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

	 initSearch("<?php echo $qobj['area']; ?>", <?php echo $session->getPageSize(); ?>);

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
		window.location="https://intern.hgk.fhnw.ch/ango/shib/auth/mediathek?callback=<?php echo urlencode( 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'&token=_JWT_'); ?>";
		<?php } ?>

	});


}
</script>

<script src="js/threejs/build/three.js"></script>
<script src="js/threejs/build/TrackballControls.js"></script>
<script src="js/threejs/build/OrbitControls.js"></script>
<script src="js/threejs/build/CombinedCamera.js"></script>
<script src="js/mediathek.js"></script>
<script src="js/mediathek3d.js"></script>
<script src="js/threex.domresize.js"></script>

<?php
echo mediathekfooter();

$pagestr .= ob_get_contents();
ob_end_flush();

file_put_contents($fullcachepath, gzencode($pagestr));


?>
