<?php

namespace Mediathek;

include '../init.inc.php';

if( true ) {

$query = $solrclient->createSelect();
$query->setFields(array('cluster'));

$prefetch = $solrclient->getPlugin('prefetchiterator');
$prefetch->setPrefetch(1000); //fetch 2 rows per query (for real world use this can be way higher)
$prefetch->setQuery($query);

$total = count($prefetch);

$sql = "DELETE FROM themes";
$db->Execute( $sql );

// show document IDs using the resultset iterator
$count = 0;
foreach ($prefetch as $doc) {
    $count++;
    if( !isset( $doc->cluster )) continue;
    foreach( $doc->cluster as $cluster ) {
        //$sql = "INSERT IGNORE INTO themes( theme) VALUES( ".$db->qstr( preg_replace( '/[^\d\w\s'.preg_quote( ",.()[]-/'\"", '/' ).']/', '', trim( strtolower( $cluster )))).");";
		$sql = "INSERT IGNORE INTO themes( theme) VALUES( ".$db->qstr($cluster).");";
		
        echo sprintf( "%02.2f % 7d/%d - ", ($count/$total)*100.0, $count, $total )."{$cluster}\n";
        $db->Execute( $sql );
    }
}

}

$sql = "DELETE FROM themes_source";
$db->Execute( $sql );

$sql = "SELECT theme FROM themes";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
    $query = $solrclient->createSelect();
    $helper = $query->getHelper();
    $query->setFields(array('cluster'));
    $groupComponent = $query->getGrouping(); 
    $groupComponent->addField('source');
    
    $query->createFilterQuery('cluster')->addTag('cluster')->setQuery('cluster_ss:'.$helper->escapePhrase( $row['theme'] ));
    
    $resultset = $solrclient->select( $query );
    $groups = $resultset->getGrouping();
    foreach ($groups as $groupkey => $group) {
        foreach( $group as $key => $member ) {
//            var_dump( $member );
            echo "{$row['theme']} - ".$member->GetValue().": ".$member->getNumFound()."\n";
            $sql = "INSERT INTO themes_source( theme, source, count ) VALUES( ".$db->qstr( $row['theme'] ).", ".$db->qstr( $member->GetValue() ).", ".$db->qstr( $member->getNumFound())." )";
            $db->Execute( $sql );
        }
    }
}

?>