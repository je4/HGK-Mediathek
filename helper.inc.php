<?php

function loadFromDatabase( $db, $c ) {
	$m = array();
	$sql = "SELECT mm.tag, mm.value FROM mediathek_marc mm, mediathek_code mc WHERE mm.sys=mc.sys AND mc.code=".$db->qstr($c);

	echo "<!-- $sql -->\n";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		$tag = $row['tag'];
		$value = $row['value'];
		if( !isset( $m[$tag] )) $m[$tag] = array();
		$vals = explode( '|', $value );
		$valarr = array();
		foreach( $vals as $v ) {
			if( !strlen( $v )) continue;
			$valarr[$v{0}] = utf8_encode( substr( $v, 2 ));
		}
		$m[$tag][] = $valarr;
	}
	$rs->Close();

	if( sizeof( $m ) == 0 ) return null;
	
	$index = 0;
	if( isset( $m['949'] )) foreach( $m['949'] as $key=>$val ) {
		if( $val['0'] == $c ) $index = $key;
	}
	$m['index'] = $index;
	return $m;
}

function findsub( $m, $tag, $sub ) {
	$i = $m['index'];
	return isset( $m[$tag][$i][$sub] ) ? trim($m[$tag][$i][$sub]) : '';
}


?>