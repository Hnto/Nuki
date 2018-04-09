<?php
namespace Nuki\Handlers\Http;

use Nuki\Skeletons\Handlers\BaseHandler;
use Nuki\Exceptions\Base;

class Session implements BaseHandler {
        
    /**
     * Contains the session id
     * 
     * @var string
     */
    private $sessionId;

    /**
     * Start the session and assign session id
     */
    public function start() {
        session_start();

        $this->sessionId = session_id();
    }
    
    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $filter = FILTER_DEFAULT) {        
        if (empty($key)) {
            throw new Base('Session key cannot be empty');
        }
        
        if (empty($value)) {
            throw new Base('Session value cannot be empty');
        }
        
        $sessionValue = filter_var($value, $filter);
        
        $_SESSION[$key] = $sessionValue;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($key = false) {
        if ($key === false) {
            return $_SESSION;
        }
        
        if (!$this->has($key)) {
            return null;
        }
        
        return $_SESSION[$key];
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Destroy session data
     * 
     * @return bool
     */
    public function destroy() {
        return session_destroy();
    }
    
    /**
     * Generate new session id
     * 
     * @param string $destroyOldSession
     * @return bool
     */
    public function generateSessionId($destroyOldSession = false) {
        return session_regenerate_id($destroyOldSession);
    }
    
    /**
     * Get the session id
     * 
     * @return string
     */
    public function getSessionId() {
        return $this->sessionId;
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($key): bool {
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $filter = FILTER_DEFAULT) {
        if ($this->has($key)) {
            $this->remove($key);
            $this->add($key, $value, $filter);
            
            return true;
        }
        
        $this->add($key, $value, $filter);
    }
}
