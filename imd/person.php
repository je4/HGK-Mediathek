<?php

namespace Mediathek;

//header( "Content-Type: image/svg+xml" );

include '../init.inc.php';

$pid = $_REQUEST['pid'];
$category = array_key_exists( 'cat', $_REQUEST ) ? $_REQUEST['cat'] : 'Brief';

$fname = 'cache/'.preg_replace(
  array('/[^a-zA-Z0-9 ._-]/'),
  array('_'), iconv('UTF-8', 'ASCII//TRANSLIT', $category)).'_'.str_replace( '-', '_', $pid ).'.gv';

if( !file_exists( $fname.'.svg' )) {
  $fp = fopen( $fname, 'w+' );
  fputs( $fp, "graph G {\n");
    // fputs( $fp, "   graph [splines=ortho, nodesep=1, overlap=false];\n" );
    fputs( $fp, "   bgcolor=\"#000000\" color=\"#ffffff\" fontcolor=\"#ffffff\"\n" );
    fputs( $fp, "   graph [nodesep=1, overlap=false, bgcolor=\"#000000\", color=\"#ffffff\", fontcolor=\"#ffffff\"];\n" );
    fputs( $fp, "   edge [color=\"#ffffff\"];\n" );
    fputs( $fp, "   node [fillcolor=\"#000000\", color=\"#ffffff\", fontcolor=\"#ffffff\", style=filled];\n" );
  $nodes = array();

  $sql = "SElECT DISTINCT * FROM (
          SELECT DISTINCT p1id AS pid FROM imd.cache_category_graph cg WHERE category=".$db->qstr( $category )." AND (cg.p2id=".$db->qstr( $pid ).")
          UNION
          SELECT DISTINCT p2id AS pid FROM imd.cache_category_graph cg WHERE category=".$db->qstr( $category )." AND (cg.p1id=".$db->qstr( $pid ).")
          ) x";


//  $sql = "SELECT DISTINCT p1id, p2id FROM imd.cache_category_graph cg WHERE category=".$db->qstr( $category )." AND (cg.p1id=".$db->qstr( $pid )." OR cg.p2id=".$db->qstr( $pid ).")";
  $sql = "SELECT DISTINCT p1id, p2id
      FROM imd.cache_category_graph cg
      WHERE category=".$db->qstr( $category )."
        AND (cg.p1id IN ({$sql})
          OR cg.p2id IN ({$sql}))";
          //echo $sql;
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    $p1id = str_replace('-', '_', $row['p1id']);
    $p2id = str_replace('-', '_', $row['p2id']);
    $nodes[$row['p1id']] = true;
    $nodes[$row['p2id']] = true;
//    $nodes[$p1id] = trim(preg_replace( '/[\[\]"]/','', $row['name1'] ));
//    $nodes[$p2id] = trim(preg_replace( '/[\[\]"]/','', $row['name2'] ));
  //  fputs( $fp, "   p{$p1id} -- p{$p2id} [weight={$row['weight']}];\n");
    fputs( $fp, "   p{$p1id} -- p{$p2id};\n");
  }
  $rs->Close();
  foreach( $nodes as $key=>$val) {
    $sql = "SELECT name FROM imd.tmp_thesaurus WHERE pid=".$db->qstr( $key );
    $val =  trim(preg_replace( '/[\[\]"]/','', $db->getOne( $sql )));
    $param = ( $key == $pid ) ? ' fontcolor=black color="#ffffff" fillcolor="#ffffff"' : '';
    fputs( $fp, "   p".str_replace( '-', '_', $key )." [label=\"{$val}\" URL=\"{$_SERVER['SCRIPT_URI']}?cat={$category}&amp;pid={$key}\" target=_top{$param}]; \n" );
  }
  fputs( $fp, "}\n");
  fclose( $fp );

  $cmd = 'circo '.$fname.' > '.$fname.'.circo.gv';
//  exec( $cmd );
  $cmd = 'neato -n -Tsvg '.$fname.'.circo.gv -o'.$fname.'.svg';
//  exec( $cmd );

  $cmd = 'neato -Tsvg '.$fname.' -o'.$fname.'.svg';
  exec( $cmd );
}
//readfile( $fname.'.svg' );
?><html>
<head>
    <meta charset="UTF-8">
    <title>HGK Mediathek</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="description" content="FHNW HGK Mediathek">
    <meta name="keywords" content="Media, Library, Book, Digital">

		<style>
			body { margin: 0; overflow:hidden; color: white; background-color: black;}
			canvas { width: 100%; height: 100vh; }
		</style>


</head>
<body class="skin-black">


    <object id="demo-tiger" type="image/svg+xml" data="<?php echo $fname; ?>.svg" style="width: 100%; height: 100%; border:1px solid black; ">Your browser does not support SVG</object>
	  <!-- <embed id="demo-tiger" type="image/svg+xml" style="width: 100%; height: 100%; border:1px solid black;" src="<?php echo $fname; ?>.svg" /> -->

    <script src="svg-pan-zoom.min.js"></script>
    <script>
       // Don't use window.onLoad like this in production, because it can only listen to one function.
       window.onload = function() {
         svgPanZoom('#demo-tiger', {
           zoomEnabled: true,
           controlIconsEnabled: true,
           maxZoom: 100
         });
       };
     </script>
  </body>
</html>
