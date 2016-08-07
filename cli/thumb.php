#!/usr/bin/php

<?php
/*

convert info.png -thumbnail x162 -crop 230x162+6+0 -sharpen 4 -strokewidth 0 -fill "rgba( 0, 0, 0 , 0.5 )" -draw "rectangle 0,0 230,20" -draw "rectangle 0,147 230,162" -font DejaVu-Sans-Mono-Bold  -pointsize 18 -gravity NorthWest -fill white -annotate +10+0 "Information" -pointsize 12 -gravity South -annotate +0+0 "Mediathek HGK" thumb/info.png

*/

$basedir = "../img/3d/Screenshots";
$thumbdir = "../img/3dbox/thumb";

$d = dir( $basedir );
while (false !== ($sub = $d->read())) {
    if( $sub{0} == '.' || strlen( $sub ) > 1 ) continue;
    
    if( is_dir( "{$basedir}/{$sub}")) {
		echo $sub."\n";
        $d2 = dir( "{$basedir}/{$sub}" );
        while (false !== ($sub2 = $d2->read())) {
            if( $sub2{0} == '.' ) continue;
			echo "   ".$sub2."\n";			
            if( is_dir( "{$basedir}/{$sub}/{$sub2}")) {
                $d3 = dir( "{$basedir}/{$sub}/{$sub2}" );
                while (false !== ($img = $d3->read())) {
                    if( preg_match( '/([A-Z])([0-9]{3})([ab]).png/', $img, $matches )) {
                        $sfile = "{$basedir}/{$sub}/{$sub2}/{$img}";
						$tfile = "{$thumbdir}/{$img}";
//                        $tfile = str_replace( '.png', '.jpg', $tfile );
                        $txt = "Reihe {$matches[1]} Box {$matches[2]} {$matches[3]}";
                        echo $sfile.' '.$txt."\n";
                        $cmd = 'convert '.escapeshellarg( $sfile ).' -thumbnail x162 -crop 230x162+6+0 -sharpen 4 -strokewidth 0 -fill "rgba( 0, 0, 0 , 0.5 )" -draw "rectangle 0,0 230,20" -draw "rectangle 0,147 230,162" -font DejaVu-Sans-Mono-Bold  -pointsize 18 -gravity NorthWest -fill white -annotate +10+0 "'.$txt.'" -pointsize 12 -gravity SouthWest -annotate +10+0 "Mediathek HGK" '.escapeshellarg( $tfile );
                        echo $cmd."\n";
                        passthru( $cmd );
						$cmd = 'exiv2 -m 3dexif.txt '.escapeshellarg( $tfile );
                        echo $cmd."\n";
                        passthru( $cmd );
                    }
                }
                $d3->close();
            }
        }
        $d2->close();
    }
}
$d->close();

?>