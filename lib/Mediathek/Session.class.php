<?php
namespace Mediathek;

class Session implements \SessionHandlerInterface
{
    // session timeout in sekunden
    static public $timeout = 3600;

  private $alive = true;
  private $db = NULL;
  private $server = null;
  private $id = null;
  private $name = null;
  private $groups = null;
  private $certEmail = null;

  private $subnetsFHNW = array();
  private $localFHNW = false;

  function __construct( $db, $server )
  {
	$this->subnetsFHNW[] = new IPv6Net( "10.0.0.0/8" );
	$this->subnetsFHNW[] = new IPv6Net( "192.168.0.0/16" );
	$this->subnetsFHNW[] = new IPv6Net( "147.86.0.0/16" );

    $this->db = $db;
    $this->server = $server;
    session_set_save_handler( $this, true );

    $this->startSession();
    $this->checkUser();
  }

   public function startSession() {
	    global $_SERVER;

    session_start();

    $this->id = session_id();

    $sql = "SELECT *, UNIX_TIMESTAMP( lastaccess) AS unix_lastaccess FROM session WHERE php_session_id=".$this->db->qstr( $this->id );
//	echo $sql;
    $row = $this->db->GetRow( $sql );
//	var_dump( $row );

	if( isset( $_SERVER['SSL_CLIENT_S_DN'])) {
		if( $_SERVER['SSL_CLIENT_I_DN'] == 'CN=Mediathek CA,OU=Certification Authority,O=Mediathek,O=FHNW Academy of Art and Design,L=Basel,ST=Basel Stadt,C=CH'
			&& $_SERVER['SSL_CLIENT_VERIFY'] == 'SUCCESS'
			&& isset( $_SERVER['SSL_CLIENT_S_DN_Email'] ) ) {
			$this->certEmail = $_SERVER['SSL_CLIENT_S_DN_Email'];
		}
	}

    // Fall 1: php session ist abgelaufen
    if( $row != null && (( time() - $row['unix_lastaccess']) > self::$timeout )) {
        // start new session
        session_regenerate_id();
		$this->id = session_id();
        $sql = 'INSERT INTO session (`uniqueID`, `Shib-Session-ID`, certEmail, `php_session_id`, clientip )
            VALUES (
                '.$this->db->qstr($this->shibGetUniqueID()).'
                , '.$this->db->qstr($this->shibGetSessionID()).'
                , '.$this->db->qstr($this->certEmail).'
                , '.$this->db->qstr($this->id).'
                , '.$this->db->qstr($_SERVER['REMOTE_ADDR']).'
                )';
//		echo $sql;
        $this->db->Execute( $sql );
    }
    // Fall 2: keine Session
    elseif( $row == null ) {
        $sql = 'INSERT INTO session (`uniqueID`, `Shib-Session-ID`, certEmail, `php_session_id`, clientip )
            VALUES (
                '.$this->db->qstr($this->shibGetUniqueID()).'
                , '.$this->db->qstr($this->shibGetSessionID()).'
                , '.$this->db->qstr($this->certEmail).'
                , '.$this->db->qstr($this->id).'
                , '.$this->db->qstr(isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null).'
                )';
//		echo $sql;
        $this->db->Execute( $sql );
    }
    // Fall 3: Shibboleth-Session existiert, ist aber noch nicht eingetragen
    elseif( $this->shibGetSessionID() != null && $row['Shib-Session-ID'] == null ) {
        $sql = "UPDATE session
            SET `uniqueID`=".$this->db->qstr($this->shibGetUniqueID()).", `Shib-Session-ID`=".$this->db->qstr($this->shibGetSessionID())."
            WHERE php_session_id=".$this->db->qstr( $this->id );
//		echo $sql;
        $this->db->Execute( $sql );
    }
    // Fall 4: Shibboleth-Session existiert, entspricht aber nicht der gespeicherten Shib-Session-ID
    // Fall 5: Shibboleth-Session existiert, PHP Session ist abgelaufen
    elseif(( $this->shibGetSessionID() != null && $row['Shib-Session-ID'] != $this->shibGetSessionID() )
           || ($this->shibGetSessionID() != null && ((( time() - $row['unix_lastaccess']) > self::$timeout )
                                                    || $row['end'] != null )))
    {
        // start new session
        $this->id = session_regenerate_id();
        $sql = 'INSERT INTO session (`uniqueID`, `Shib-Session-ID`, certEmail, `php_session_id`, clientip )
            VALUES (
                '.$this->db->qstr($this->shibGetUniqueID()).'
                , '.$this->db->qstr($this->shibGetSessionID()).'
                , '.$this->db->qstr($this->certEmail).'
                , '.$this->db->qstr($this->id).'
                , '.$this->db->qstr($_SERVER['REMOTE_ADDR']).'
                )';
//		echo $sql;
        $this->db->Execute( $sql );
    }
    else {
        $sql = "UPDATE session SET lastaccess=NOW() WHERE php_session_id=".$this->db->qstr($this->id);
//		echo $sql;
        $this->db->Execute( $sql );
    }
  }

  public function checkUser() {

    static $fields = array( 'uniqueID', 'mail', 'homeOrganization', 'homeOrganizationType', 'uid', 'givenName', 'surname', 'telephoneNumber', 'affiliation', 'entitlement', 'employeeNumber', 'orgunit-dn' );

    if( $this->shibGetUniqueID() == null ) return;


    $sql = "SELECT * FROM user WHERE uniqueID=".$this->db->qstr( $this->shibGetUniqueID() );
    $row = $this->db->GetRow( $sql );
    // 1. Fall: User existiert nicht
    if( $row == null ) {
        $sql = "INSERT INTO user( ";
        $first = true;
        foreach( $fields as $fld ) {
            if( $first ) $first = false;
            else $sql .= ", ";
            $sql .= "`{$fld}`";
        }
        $sql .= ") VALUES (";
        $first = true;
        foreach( $fields as $fld ) {
            if( $first ) $first = false;
            else $sql .= ", ";
            $sql .= $this->db->qstr( isset( $this->server[$fld] ) ? $this->server[$fld] : null );
        }
        $sql .= ");";
        $this->db->Execute( $sql );
    }
  }

  public function getID() {
	return $this->id;
  }

  public function isLoggedIn() {
    return $this->shibGetSessionID() != null;
  }

  public function isAdmin() {
    global $config;
    return $this->inGroup( $config['admingroup'] );
  }
  public function shibGetSessionID() {
    return isset( $this->server['Shib-Session-ID'] ) ? $this->server['Shib-Session-ID'] : null;
  }

  public function shibGetUniqueID() {
    return isset( $this->server['uniqueID'] ) ? $this->server['uniqueID'] : null;
  }

  public function shibGetMail() {
    return isset( $this->server['mail'] ) ? $this->server['mail'] : null;
  }

  public function shibGetUsername() {
    return (isset( $this->server['givenName'] ) ? "{$this->server['givenName']} " : "" ).(isset( $this->server['surname'] ) ? $this->server['surname'] : "");
  }

  public function shibGetGivenName() {
    return (isset( $this->server['givenName'] ) ? "{$this->server['givenName']}" : "" );
  }

  public function shibGetSurname() {
    return (isset( $this->server['surname'] ) ? $this->server['surname'] : "");
  }

  public function shibGetEmployeenumber() {
    return (isset( $this->server['employeeNumber'] ) ? $this->server['employeeNumber'] : "");
  }

  public function shibGetUID() {
    return (isset( $this->server['uid'] ) ? $this->server['uid'] : "");
  }

  public function shibHomeOrganization() {
    return isset( $this->server['homeOrganization'] ) ? $this->server['homeOrganization'] : null;
  }

  public function shibAffiliation() {
    return isset( $this->server['affiliation'] ) ? $this->server['affiliation'] : null;
  }

  public function shibDepartement() {
	  if( isset( $this->server['orgunit-dn'] ))
		if( preg_match( "/OU=([A-Za-z0-9]+)(,OU=([A-Z]+))?,OU=([0-9]+),[a-zA-Z,=],DC=fhnw,DC=ch/", $this->server['orgunit-dn'], $matches )) {
			return $matches[1].$matches[2].$matches[4];
		}
	  return null;
  }

  function storeQuery( $queryid ) {
  	$sql = "SELECT count(*) FROM session_query WHERE php_session_id=".$this->db->qstr( $this->id )." AND queryid=".$this->db->qstr( $queryid );
  	$num = intval( $this->db->GetOne( $sql ));
  	if( !$num )
  		$sql = "INSERT INTO session_query VALUES( ".$this->db->qstr( $this->id ).", ".$this->db->qstr( $queryid ).", 1, NOW())";
  		else
  			$sql = "UPDATE session_query SET counter=counter+1, accesstime=NOW() WHERE php_session_id=".$this->db->qstr( $this->id )." AND queryid=".$this->db->qstr( $queryid );
  	  $this->db->Execute( $sql );
  }

  function storeCustom( $task ) {
  	$sql = "SELECT count(*) FROM session_custom WHERE php_session_id=".$this->db->qstr( $this->id )." AND task=".$this->db->qstr( $task );
  	$num = intval( $this->db->GetOne( $sql ));
  	if( !$num )
  		$sql = "INSERT INTO session_custom VALUES( ".$this->db->qstr( $this->id ).", ".$this->db->qstr( $task ).", 1, NOW())";
	else
		$sql = "UPDATE session_custom SET counter=counter+1, accesstime=NOW() WHERE php_session_id=".$this->db->qstr( $this->id )." AND task=".$this->db->qstr( $task );
    $this->db->Execute( $sql );
  }

  function getGroups() {
	global $_SERVER;

//    if( !$this->isLoggedIn() ) return array('global/guest');

    if( $this->groups != null ) return $this->groups;

    $this->groups = array( 'global/guest' );

	foreach( $this->subnetsFHNW as $sub ) {
	  if( $sub->contains( $_SERVER['REMOTE_ADDR'])) {
		$this->groups[] = "location/fhnw";
		$this->localFHNW = true;
		break;
	  }
	}

	if( $this->isLoggedIn()) {
    $this->groups[] = "global/user";
	  $this->groups[] = $this->shibHomeOrganization().'/user';
	  $dept = $this->shibDepartement();
	  if( $dept != null )
		  $this->groups[] = $this->shibHomeOrganization().':'.$dept.'/user';
	  foreach( explode( ';', $this->shibAffiliation()) as $grp )
		  $this->groups[] = $this->shibHomeOrganization().'/'.strtolower( trim( $grp ));

	  if( isset( $this->server['orgunit-dn'] ))
		if( preg_match( '/^.*,OU=([0-9]+),OU=.+,OU=.+,DC=.+,DC=ds,DC=fhnw,DC=ch/', $_SERVER['orgunit-dn'], $matches )) {
			$this->groups[] = "fhnw.ch_{$matches[1]}/user";
  //		  $this->groups[] = strtolower( "fhnw.ch:{$matches[3]}:{$matches[1]}/user" );
		}
    if( preg_match( '/^.*,OU=([0-9a-zA-Z]+),OU=([0-9]+),OU=.+,OU=.+,DC=.+,DC=ds,DC=fhnw,DC=ch/', $_SERVER['orgunit-dn'], $matches )) {
      $this->groups[] = "fhnw.ch_{$matches[2]}/".strtolower($matches[1]);
    //		  $this->groups[] = strtolower( "fhnw.ch:{$matches[3]}:{$matches[1]}/user" );
    }
	}
    $sql = "SELECT grp FROM groups WHERE uniqueID=".$this->db->qstr( $this->shibGetUniqueID())." OR uniqueID=".$this->db->qstr( strtolower( $this->shibGetMail()));
    $rs = $this->db->Execute( $sql );
    foreach( $rs as $row ) {
		if( preg_match( '/^fhnw\/.*$/', $row['grp'] ) && !$this->localFHNW ) continue;
        $this->groups[] = strtolower( trim( $row['grp'] ));
    }
    $rs->Close();

	if( $this->certEmail )
		$this->groups[] = "certificate/mediathek";


    return $this->groups;
  }

  public function inGroup( $grp ) {
	$grp = strtolower( $grp );
	foreach( $this->getGroups() as $g ) {
	  if( $g == $grp ) return true;
	}
	return false;
  }

  public function inAnyGroup( $grps ) {
	foreach( $grps as $grp )
	  if( $this->inGroup( $grp ))
		return true;

	return false;
  }

  public function getPageSize() {
	return 25;
  }

  public function __destruct()
  {
    if($this->alive)
    {
      session_write_close();
      $this->alive = false;
    }
  }

  public function delete()
  {
    if(ini_get('session.use_cookies'))
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
      );
    }

    session_destroy();

    $this->alive = false;
  }


  // SessionHandler functions

  public  function open(  $save_path ,  $name )
  {
    $this->name = $name;
    return true;
  }

  public  function close()
  {
    return true;
  }

  public  function read($session_id)
  {
    $sql = "SELECT `data` FROM `session` WHERE `php_session_id`=".$this->db->qstr($session_id)." LIMIT 1";
    $data = $this->db->GetOne( $sql );

    return $data == null ? '' : $data;
  }

  public  function write( $session_id ,  $session_data)
  {
    $this->data = $session_data;
    $sql = "UPDATE `session` SET `data`=".$this->db->qstr($session_data)." WHERE php_session_id=".$this->db->qstr($session_data);
    $this->db->query($sql);

    return $this->db->Affected_Rows();
  }

  public  function destroy( $session_id)
  {
    $sql = "UPDATE `session` SET `end`=NOW() WHERE php_session_id=".$this->db->qstr($session_id);
    $this->db->query($sql);

    return $this->db->Affected_Rows();
  }

  public  function gc( $maxlifetime)
  {
    $sql = "UPDATE `session` SET `end`=NOW() WHERE DATE_ADD(`lastaccess`, INTERVAL ".(int) $maxlifetime." SECOND) < NOW()";
    $this->db->query($sql);

    return $this->db->Affected_Rows();
  }
}

?>
