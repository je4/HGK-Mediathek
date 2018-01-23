<?php

/**
 * PHP Server-Side Example for Fine Uploader (traditional endpoint handler).
 * Maintained by Widen Enterprises.
 *
 * This example:
 *  - handles chunked and non-chunked requests
 *  - supports the concurrent chunking feature
 *  - assumes all upload requests are multipart encoded
 *  - supports the delete file feature
 *
 * Follow these steps to get up and running with Fine Uploader in a PHP environment:
 *
 * 1. Setup your client-side code, as documented on http://docs.fineuploader.com.
 *
 * 2. Copy this file and handler.php to your server.
 *
 * 3. Ensure your php.ini file contains appropriate values for
 *    max_input_time, upload_max_filesize and post_max_size.
 *
 * 4. Ensure your "chunks" and "files" folders exist and are writable.
 *    "chunks" is only needed if you have enabled the chunking feature client-side.
 *
 * 5. If you have chunking enabled in Fine Uploader, you MUST set a value for the `chunking.success.endpoint` option.
 *    This will be called by Fine Uploader when all chunks for a file have been successfully uploaded, triggering the
 *    PHP server to combine all parts into one file. This is particularly useful for the concurrent chunking feature,
 *    but is now required in all cases if you are making use of this PHP example.
 */

$basedir = '/data2/mab-basel/';

setlocale(LC_CTYPE, "en_US.UTF-8");

// Include the upload handler class
require_once "handler.php";

$uploader = new UploadHandler();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
$uploader->allowedExtensions = array(); // all files types allowed by default

// Specify max file size in bytes.
$uploader->sizeLimit = null;

// Specify the input name set in the javascript.
$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
$uploader->chunksFolder = "chunks";

$method = get_request_method();

// This will retrieve the "intended" request method.  Normally, this is the
// actual method of the request.  Sometimes, though, the intended request method
// must be hidden in the parameters of the request.  For example, when attempting to
// delete a file using a POST request. In that case, "DELETE" will be sent along with
// the request in a "_method" parameter.
function get_request_method() {
    global $HTTP_RAW_POST_DATA;

    if(isset($HTTP_RAW_POST_DATA)) {
    	parse_str($HTTP_RAW_POST_DATA, $_POST);
    }

    if (isset($_POST["_method"]) && $_POST["_method"] != null) {
        return $_POST["_method"];
    }

    return $_SERVER["REQUEST_METHOD"];
}

if ($method == "POST") {
    header("Content-Type: text/plain");

    // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
    // For example: /myserver/handlers/endpoint.php?done
    if (isset($_GET["done"])) {
        $result = $uploader->combineChunks($basedir."files");
/*
        $name = $uploader->getUploadName();
        $sql = "INSERT INTO files( formid, filename ) VALUES ({$_GET['formid']}, ".$db->qstr( $name );
        $db->Execute( $sql );
*/
    }
    // Handles upload requests
    else {
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($basedir."files");

        // To return a name used for uploaded file you can use the following line.
        $result["uploadName"] = $uploader->getUploadName();
    }

    $totalParts = isset($_REQUEST['qqtotalparts']) ? (int)$_REQUEST['qqtotalparts'] : 1;
    if( isset($_GET["done"]) || $totalParts == 1 ) {
      ob_start();
      include( 'config.inc.php' );
      include( '../init.inc.php' );
      ob_end_clean();
      $id = @intval($_GET['id']);

      $uuid = $_REQUEST['qquuid'];
      $name = $uploader->getUploadName();
      $tech = null;
      $mime = mime_content_type( $basedir."files/{$uuid}/{$name}" );

      if( !is_dir( $basedir."thumbs/{$uuid}" )) mkdir( $basedir."thumbs/{$uuid}" );
      if( !is_dir( $basedir."web/{$uuid}" )) mkdir( $basedir."web/{$uuid}" );

      if( preg_match( '/^audio\//', $mime )
        || preg_match( '/^video\//', $mime )) {
        $cmd = 'ffprobe -loglevel quiet -show_format -show_streams -print_format json '.escapeshellarg( $basedir."files/{$uuid}/{$name}" )." 2>&1";
        $tech = shell_exec( $cmd );
        $tech = $cmd."\n".$tech;
        $tdata = json_decode( $tech, true );
        $isVideo = false;
        foreach( $tdata['streams'] as $stream ) {
          if( $stream['codec_type'] == 'video') $isVideo = true;
        }
        if( $isVideo ) {
          @mkdir( "thumbs/{$uuid}" );
          $cmd = 'ffmpeg -i '.escapeshellarg( "files/{$uuid}/{$name}" ).' -ss 00:00:08 -vframes 1 '.escapeshellarg( $basedir."thumbs/{$uuid}/{$name}.png" )." 2>&1";
          shell_exec( $cmd );
          $tech = $cmd."\n".$tech;
          if( file_exists("thumbs/{$uuid}/{$name}.png")) {
            $result['thumbnailUrl'] = $basedir."thumbs/{$uuid}/{$name}.png";
          }
        }
        // ffmpeg -i input.flv -ss 00:00:14.435 -vframes 1 out.png
      }
      elseif( preg_match( '/^image\//', $mime ) ) {
        $cmd = 'identify '.escapeshellarg( $basedir."files/{$uuid}/{$name}" )." 2>&1";
        $tech = shell_exec( $cmd );
        $tech = $cmd."\n".$tech;
        @mkdir( "thumbs/{$uuid}" );
        @mkdir( "web/{$uuid}" );
        shell_exec( 'convert '.escapeshellarg( $basedir."files/{$uuid}/{$name}" ).' -resize '.escapeshellarg('640x480>').' '.escapeshellarg( $basedir."thumbs/{$uuid}/{$name}.png" ));
        shell_exec( 'convert '.escapeshellarg( $basedir."files/{$uuid}/{$name}" ).' -resize '.escapeshellarg('1920x1080>').' '.escapeshellarg( $basedir."web/{$uuid}/{$name}.png" ));
        if( file_exists("thumbs/{$uuid}/{$name}.png")) {
          $result['thumbnailUrl'] = $basedir."thumbs/{$uuid}/{$name}.png";
          if( file_exists($basedir."thumbs/{$uuid}/{$name}.png")) {
            $result['thumbnailUrl'] = $basedir."thumbs/{$uuid}/{$name}.png";
          }
        }

      }
      elseif( preg_match( '/^application\/pdf/', $mime ) ) {
        $cmd = 'pdfinfo '.escapeshellarg( $basedir."files/{$uuid}/{$name}" )." 2>&1";
        $tech = shell_exec( $cmd );
        $tech = $cmd."\n".$tech;
        @mkdir( "thumbs/{$uuid}" );
        @mkdir( "web/{$uuid}" );
        shell_exec( 'convert '.escapeshellarg( $basedir."files/{$uuid}/{$name}" ).'[0] -resize '.escapeshellarg('640x480>').' '.escapeshellarg( $basedir."thumbs/{$uuid}/{$name}.png" )." 2>&1");
        shell_exec( 'convert '.escapeshellarg( $basedir."files/{$uuid}/{$name}" ).'[0] -resize '.escapeshellarg('1920x1080>').' '.escapeshellarg( $basedir."web/{$uuid}/{$name}.png" ));
        if( file_exists($basedir."thumbs/{$uuid}/{$name}.png")) {
          $result['thumbnailUrl'] = $basedir."thumbs/{$uuid}/{$name}.png";
          if( file_exists($basedir."thumbs/{$uuid}/{$name}.png")) {
            $result['thumbnailUrl'] = "https://e-medien.mab-bs.ch/thumbs/{$uuid}/{$name}.png";
          }
        }
      }

      $sql = "INSERT INTO mab_bs.upload_files( id, name, filename, thumbname, webname, mimetype, size, tech, uploadtime ) VALUES ({$id}
        , ".$db->qstr( $name )."
        , ".$db->qstr( "files/{$uuid}/{$name}" )."
        , ".(file_exists($basedir."thumbs/{$uuid}/{$name}.png") ? $db->qstr( "https://e-medien.mab-bs.ch/thumbs/{$uuid}/{$name}.png" ) : "NULL" )."
        , ".(file_exists($basedir."web/{$uuid}/{$name}.png") ? $db->qstr( "https://e-medien.mab-bs.ch/web/{$uuid}/{$name}.png" ) : "NULL" )."
        , ".$db->qstr( $mime )."
        , ".filesize( $basedir."files/{$uuid}/{$name}" )."
        , ".$db->qstr( $tech )."
        , NOW())";
      $db->Execute( $sql );
    }

    echo json_encode($result);
}
// for delete file requests
else if ($method == "DELETE") {
    $result = $uploader->handleDelete("files");
    echo json_encode($result);
}
else {
    header("HTTP/1.0 405 Method Not Allowed");
}

?>
