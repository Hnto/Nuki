<?php
namespace Nuki\Handlers\Http;

use Nuki\Skeletons\Handlers\BaseHandler;

class Cookie implements BaseHandler {

    /**
     * Contains cookie vars
     *
     * @var array
     */
    private $vars;

    /**
     * Init all headers vars
     */
    public function __construct() {
        $this->vars = $_COOKIE;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key = false, $filter = FILTER_DEFAULT, array $options = []) {
        if ($key === false) {
            return $this->vars;
        }

        if (!$this->has($key)) {
          return null;
        }
      
        return filter_var($this->vars[$key], $filter, $options);
    }

    /**
     * {@inheritdoc}
     */  
    public function add($key, $value, $filter = FILTER_DEFAULT, array $options = []) {
        $cookieValue = filter_var($value, $filter, $options);
        setcookie($key, $cookieValue);
        $this->vars[$key] = $cookieValue;
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        unset($_COOKIE[$key]);
        unset($this->vars[$key]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($key) : bool {
        if (!isset($this->vars[$key])) {
            return false;
        }
        
        return true;
    }
}
