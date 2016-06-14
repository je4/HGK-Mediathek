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

$sourcemap = array( 'nebis' => 'Buchbestand',
				    'doaj' => 'Directory of Open Access Journals',
					'ikuvid' => 'Videosammlung Institut Kunst',
				  );

// get request values
$query = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;
$q = isset( $_REQUEST['q'] ) ? strtolower( trim( $_REQUEST['q'] )): null;
$page = isset( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 0;
$pagesize = isset( $_REQUEST['pagesize'] ) ? intval( $_REQUEST['pagesize'] ) : 25;
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
    <li class="<?php echo $page <= 0 ? "disabled" : ""; ?>"><a href="#" style="border: none;" onclick="<?php if( $page >0 ) echo "pageSearch( '{$q}', ".($page-1).", {$pagesize} );"; ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i></a></li>
	<li><input type="text" style="width: 30px;" class="page" value="<?php echo ($page+1); ?>"> / <?php echo $numPages; ?></li>
    <li class="<?php echo $page >= $numPages-1 ? "disabled" : ""; ?>"><a href="#" style="border: none;" onclick="<?php if( $page < $numPages-1 ) echo "pageSearch( '{$q}', ".($page+1).", {$pagesize} );"; ?>"><i class="fa fa-chevron-right" aria-hidden="true"></i></a></li>
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
<div class="modal fade" id="3DModal" tabindex="-1" role="dialog" aria-labelledby="3DModal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div>
          <iframe width="100%" height="100%" style="min-height: 350px;" src="" scrolling="no"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>

	<div class="container-fluid" style="margin-top: 0px; background-color: rgba(255, 255, 255, 0.5); padding: 20px;">
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
	// TODO: besser machen
	$phrase = $helper->escapePhrase( $qobj->query );
	$words = explode( ' ', $qobj->query );
	$qstr = '';
	$qstr .= ' (';
	$first = true;
	foreach( $words as $word ) {
		if( !$first ) {
			$qstr .= ' AND ';
		}
		$qstr .= 'title:'.$helper->escapePhrase( $word ).'^10';
	}
	$qstr .= ' )';
	$qstr .= ' OR (';
	$first = true;
	foreach( $words as $word ) {
		if( !$first ) {
			$qstr .= ' OR ';
		}
		$qstr .= 'author:'.$helper->escapePhrase( $word ).'^10';
	}
	$qstr .= ' )';
	$qstr .= ' OR (';
	$first = true;
	foreach( $words as $word ) {
		if( !$first ) {
			$qstr .= ' AND ';
		}
		$qstr .= 'content:'.$helper->escapePhrase( $word ).'^10';
	}
	$qstr .= ' )';
	$qstr .= ' OR (';
	$first = true;
	foreach( $words as $word ) {
		if( !$first ) {
			$qstr .= ' OR ';
		}
		$qstr .= 'signature:'.$helper->escapePhrase( $word ).'^10';
	}
	$qstr .= ' )';
//    $qstr = 'title:'.$phrase.'^10 author:'.$phrase.'^8 content:'.$phrase.'^3 signature:'.$phrase.'^3';
}
else
    $qstr = '*:*';
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
//$facetSetLicense = $squery->getFacetSet();
//$facetSetLicense->createFacetField('license')->setField('license')->addExclude('license');
$facetSetCluster = $squery->getFacetSet();
$facetSetCluster->createFacetField('cluster')->setField('cluster')->addExclude('cluster');



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

buildPagination();


echo $res->getResult();

buildPagination();

?>
			  </table>
		  </div>
   		  <div class="col-md-3" style="background-color: transparent;">
			<span style="; font-weight: bold;">Katalog</span><br />
			<div class="bw" style="padding:5px;">
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

			<span style="; font-weight: bold;">Keywords</span><br />
			<div class="bw" style="padding:5px;">
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

<?php if( false ) { ?>		
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
<?php } ?>			
		  </div>

	   </div>
	</div>

	
<?php

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

	var trigger = $("body").find('[data-toggle="modal"]');
	trigger.click(function() {
		var theModal = $(this).data( "target" );
		src3d = $(this).attr( "data-3D" ); 
		$(theModal+' iframe').attr('src', src3d);
		$(theModal+' button.close').click(function () {
			$(theModal+' iframe').attr('src', src3d);
		});   
	});
	
	 $('.page').keypress(function (e) {
		if (e.which == 13) {
          var page = this.value;
		  pageSearch( '<?php echo $q; ?>', parseInt( page )-1, <?php echo $pagesize; ?> );
		  return false;    //<---- Add this line
		}
	 });

}
</script>   
<?php
echo mediathekfooter();
?>

  
