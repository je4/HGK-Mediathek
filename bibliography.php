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
$search = isset( $_REQUEST['search'] ) ? $_REQUEST['search'] : null;
$facets = isset( $_REQUEST['facets'] ) ? $_REQUEST['facets'] : null;


$query = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
$q = isset( $_REQUEST['q'] ) ? strtolower( trim( $_REQUEST['q'] )): null;
$page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 0;
// $pagesize = isset( $_REQUEST['pagesize'] ) ? intval( $_REQUEST['pagesize'] ) : 1500;
$pagesize = 1500;
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
<p />
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
		$md5 = md5($query);
		Helper::writeQuery( $md5, $qobj, $query );

		// md5-sum not valid for query
		if( $md5 != $q ) {
			header("Location: ?q={$md5}&page={$page}&pagesize={$pagesize}");
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
		$qobj = array();
		$qobj['facets'] = array();
		$qobj['filter'] = array();
		$qobj['area'] = '';
		if( is_array( $facets )) $qobj['facets'] = $facets;
		else $qobj['facets']['catalog'] = $config['defaultcatalog'];
		$qobj['query'] = $search ? $search : '*';
		$query = json_encode( $qobj );
		$q = md5($query);
		Helper::writeQuery( $q, $qobj, $query );
		$session->storeQuery( $q );

		if( $search && false ) {
			var_dump( $_REQUEST );
			exit;
		}

		header("Location: ?q={$q}&page={$page}&pagesize={$pagesize}");
		exit;
	}
	else {
		$qobj = Helper::readQuery( $q );
		$session->storeQuery( $q );
	}
}

if( $qobj['query'] == '' ) $qobj['query'] = '*';
/*
	echo "<!-- ";
	var_dump( $qobj );
	echo " -->\n";
*/



echo mediathekheader('search', 'mediathek - Suche - '.$qobj['query'], $qobj['area']);
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
                    <h2 class="small-heading">Mediathek</h2>

	<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
		<div class="row" style="margin-bottom: 30px;">
		  <div class="col-md-offset-2 col-md-8">
<?php

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( $pagesize );
$squery->setStart( $page * $pagesize );
$squery->addSort('year', $squery::SORT_DESC);

if( $qobj['query'] != '*' ) {
	$qstr = Helper::buildSOLRQuery( $qobj['query'] );
}
else
    $qstr = '*:*';

echo "<!-- qstr: {$qstr} -->\n";

$acl_query = '';
foreach( $session->getGroups() as $grp ) {
	if( strlen( $acl_query ) > 0 ) $acl_query .= ' OR';
	$acl_query .= ' acl_meta:'.$helper->escapePhrase($grp);
}
$squery->createFilterQuery('acl_meta')->setQuery($acl_query);
echo "<!-- filter acl_meta: {$acl_query} -->\n";

$squery->createFilterQuery('nodelete')->setQuery('deleted:false');
echo "<!-- filter nodelete: deleted:false -->\n";

if( @is_array( $qobj['facets']['catalog'] )) {
	$catalogfilterquery = "";
	foreach( $qobj['facets']['catalog'] as $cat ) {
		if( $catalogfilterquery != '' ) $catalogfilterquery .= ' OR ';
		$catalogfilterquery .= ' (catalog:'.$helper->escapePhrase( $cat ).')';
	}
	$squery->createFilterQuery('catalog')->addTag('catalog')->setQuery( $catalogfilterquery );
	echo "<!-- filter catalog: {$catalogfilterquery} -->\n";

}
if( DEBUG ) {
	if( @is_array( $qobj['facets']['category'] )) {
		$categoryfilterquery = "";
		foreach( $qobj['facets']['category'] as $cat ) {
			if( $categoryfilterquery != '' ) $categoryfilterquery .= ' OR ';
			$categoryfilterquery .= ' (category:'.$helper->escapePhrase( $cat ).')';
		}
	    $squery->createFilterQuery('category')->addTag('category')->setQuery( $categoryfilterquery );
	    echo "<!-- filter category: {$categoryfilterquery} -->\n";

	}
}
if( @is_array( $qobj['facets']['online'] )) {
	$onlinefilterquery = 'online:('.implode(' ', $qobj['facets']['online']).')';
	$squery->createFilterQuery('online')->addTag('online')->setQuery($onlinefilterquery);
	echo "<!-- filter online: {$onlinefilterquery} -->\n";

}

if( @is_array( $qobj['facets']['openaccess'] )) {
	$openaccessfilterquery = 'openaccess:('.implode(' ', $qobj['facets']['openaccess']).')';
	$squery->createFilterQuery('openaccess')->addTag('openaccess')->setQuery($openaccessfilterquery);
	echo "<!-- filter openaccess: {$openaccessfilterquery} -->\n";

}

/*
	if( @is_array( $qobj['facets']['embedded'] )) {
		$embeddedfilterquery = 'embedded:('.implode(' ', $qobj['facets']['embedded']).')';
		$squery->createFilterQuery('embedded')->addTag('embedded')->setQuery($embeddedfilterquery);
		echo "<!-- filter embedded: {$embeddedfilterquery} -->\n";

	}
*/
if( @is_array( $qobj['facets']['type'] )) {
	$typefilterquery = 'type:('.implode(' ', $qobj['facets']['type']).')';
	$squery->createFilterQuery('type')->addTag('type')->setQuery($typefilterquery);
	echo "<!-- filter type: {$typefilterquery} -->\n";

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
    echo "<!-- filter cluster: {$_qstr} -->\n";

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
	echo "<!-- filter license: {$licensefilterquery} -->\n";
}

$facetSetLicense = $squery->getFacetSet();
$facetSetLicense->createFacetField('license')->setField('license')->addExclude('license');

if( DEBUG ) {
	$facetSetCategory = $squery->getFacetSet();
	$facetSetCategory->createFacetField('category')->setField('category')->setLimit( $config['categorylimit'] )->addExclude('category');
}
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

//$squery->setMethod(\Solarium\Core\Client\Request::METHOD_GET);

try {
	$rs = $solrclient->select( $squery );
}
catch ( \Exception $e ) {
	echo "Error: {$e}\n";
	echo $e->getTraceAsString();
	die();
}
$numResults = $rs->getNumFound();
$numPages = floor( $numResults / $pagesize );
if( $numResults % $pagesize > 0 ) $numPages++;

echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".($page+1)." of {$numPages}) -->\n";

//$res = new DesktopResult( $rs, $page * $pagesize, $pagesize, $db, $urlparams );


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
    $highlighting = $rs->getHighlighting();
    $content = array();
    foreach( $rs as $doc ) {
        if( $highlighting ) $highlightedDoc = $highlighting->getResult($doc->id);
        else $highlightedDoc = null;
        $class = '\\Mediathek\\'.$doc->source.'Entity';
        $entity = new $class($db);
        $entity->loadFromDoc( $doc );
        //echo $entity->getTitle()."<br />";
        if( $doc->source != 'zotero' ) continue;
        $item = $entity->getItem();
        $cols = $item->getCollections();
        foreach( $cols as $col ) {
          $fn = $col->getFullName();
          if( !array_key_exists( $fn, $content )) $content[$fn] = array();
          $content[$fn][] = $entity;
          //echo "  ".$col->getFullName()."<br />";
        }
    }
    ksort( $content );
    foreach( array_keys($content) as $key ) {
      usort( $content[$key], function($ea, $eb) {
        $a = intval($ea->getYear());
        $b = intval($eb->getYear());
        //echo "{$a}--{$b}<br />\n";
        if( $a == $b ) return 0;
        if( $a < $b ) return 1;
        else return -1;
      });
    }
?>
<div id="bib" role="tablist" aria-multiselectable="true">

<?php
    foreach( $content as $key=>$entities ) {
			if( substr( $key, 0, 4) == 'Test' ) continue;
      $md5 = md5( $key );
?>
<div class="card">
  <div class="card-header" role="tab" id="q<?php echo $md5; ?>">
    <h5 class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#bib" href="#a<?php echo $md5; ?>" aria-expanded="true" aria-controls="answerThree">
      <?php echo htmlspecialchars( str_replace( ':', ' >> ', $key) ); ?>
      </a>
    </h5>
  </div>
  <div id="a<?php echo $md5; ?>" class="collapse" role="tabcard" aria-labelledby="q<?php echo $md5; ?>">
    <div class="card-block">

    <?php
    foreach( $entities as $entity ) {
      if( count( $entity->getItem()->getPDFs())) {
        echo '<a href="https://mediathek.hgk.fhnw.ch/dev/detail.php?id='.urlencode($entity->getID()).'">'.$entity->cite( 'apa-de.csl', 'ch', 'bibliography' )."</a>\n";
      }
      else {
        echo ''.$entity->cite( 'apa-de.csl', 'ch', 'bibliography' )."\n";
      }
//      echo '<a href="https://mediathek.hgk.fhnw.ch/dev/detail.php?id='.urlencode($item->getID()).'">&rarr;</a>'."<br />\n";
    }
    ?>
    </div>
  </div>
</div>

<?php
    }
?>
</div>
<?php
//		echo $res->getResult();
		buildPagination();
	}

?>
			  </table>
			</div>
		  </div>
			<!--
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

<?php if( DEBUG ) { ?>
<?php } /* DEBUG */ ?>


		  </div>
		-->

	   </div>
	</div>
	<div class="footer clearfix">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 col-xs-10 col-xs-offset-1">
				<div class="row">
					<div class="col-md-6 col-sm-12 col-xs-12">
						<p class="copyright">Copyright &copy; 2016
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

	 initSearch("<?php echo $qobj['area']; ?>", <?php echo $session->getPageSize(); ?>);

	$('body').on('click', '.setting-btn', function () {
		<?php if( $session->isLoggedIn()) { ?>
		window.location="settings.php";
		<?php } else { ?>
		window.location="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>";
		<?php } ?>

	});


}
</script>

<?php
echo mediathekfooter();
?>
