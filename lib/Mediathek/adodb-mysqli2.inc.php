<?php
/*
@version   v0.10.1
@copyright (c) 2017 Jürgen Enge and the ADODB Community. All rights reserved.
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 8.

*/

/*
// security - hide paths
if (!defined('ADODB_DIR')) die();

if (! defined("_ADODB_MYSQLI_LAYER")) {
 define("_ADODB_MYSQLI_LAYER", 1 );

 // PHP5 compat...
 if (! defined("MYSQLI_BINARY_FLAG"))  define("MYSQLI_BINARY_FLAG", 128);
 if (!defined('MYSQLI_READ_DEFAULT_GROUP')) define('MYSQLI_READ_DEFAULT_GROUP',1);

 // disable adodb extension - currently incompatible.
 global $ADODB_EXTENSION; $ADODB_EXTENSION = false;
*/

require_once( 'adodb-mysqli.inc.php' );

class ADODB_mysqli2 extends ADODB_mysqli {
	var $argHostname = NULL;
	var $argUsername = NULL;
	var $argPassword = NULL;
	var $argDatabasename = NULL; 
	var $persist=false;
	
	function __construct()
	{
		// if(!extension_loaded("mysqli"))
		//trigger_error("You must have the mysqli extension installed.", E_USER_ERROR);
	}

	// returns true or false
	// To add: parameter int $port,
	//         parameter string $socket
	function _connect($argHostname = NULL,
				$argUsername = NULL,
				$argPassword = NULL,
				$argDatabasename = NULL, $persist=false)
	{
		$this->argHostname = $argHostname;
		$this->argUsername = $argUsername;
		$this->argPassword = $argPassword;
		$this->argDatabasename = $argDatabasename;
		$this->persist = $persist;
		
		return parent::_connect( $argHostname, $argUsername, $argPassword, $argDatabasename, $persist );
	}

	function _Execute($sql,$inputarr=false) {
		$retry = false;
		do {
			try{
				return parent::_Execute( $sql, $inputarr );
			}
			catch( ADODB_Exception $ex ) {
				if( $retry ) throw $ex;
				usleep( 100000 );
				parent::_connect( $this->argHostname, $this->argUsername, $this->argPassword, $this->argDatabasename, $this->persist );
				$retry = true;
			}
		}
		while( $retry );
	}

}
