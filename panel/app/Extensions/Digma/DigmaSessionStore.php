<?php
namespace App\Extensions\Digma;

use Session;
use SessionHandlerInterface;

class DigmaSessionStore implements SessionHandlerInterface
{
    
    /**
     * @var array list of known subnets
     */
    private $subnets = array();
    
    /**
     * Re-initialize existing session, or creates a new one. Called when a session starts or when session_start() is invoked.
     * @param string $savePath The path where to store/retrieve the session.
     * @param string $sessionName The session name.
     * @return The return value (usually TRUE on success, FALSE on failure). Note this value is returned internally to PHP for processing.
     */
    public function open( string $savePath, string $sessionName) {
    }
    
    public function close() {}
    public function read($sessionId) {}
    public function write($sessionId, $data) {}
    public function destroy($sessionId) {}
    public function gc($lifetime) {}
}

