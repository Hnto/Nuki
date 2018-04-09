<?php
namespace Nuki\Handlers\Http;

use Nuki\Skeletons\Handlers\BaseHandler;

class Headers implements BaseHandler {

    /**
     * Contains header vars
     *
     * @var array
     */
    private $vars;

    /**
     * Init all headers vars
     */
    public function __construct() {
        $this->vars = getallheaders();
    }

    /**
     * {@inheritdoc}
     */
    public function get($var = false, $filter = FILTER_DEFAULT, array $options = []) {
        if ($var === false) {
            return $this->vars;
        }
        
        if (!$this->has($var)) {
          return null;
        }
        
        return filter_var($this->vars[$var], $filter, $options);
    }

    /**
     * {@inheritdoc}
     */ 
    public function add($key, $value) {
      header('' . $key . ': ' . $value . '', false);
      $this->vars[$key] = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($key) {
      unset($this->vars[$key]);
      header_remove($key);
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
    
    /**
     * Alias of add
     * But if key exists, it will be replaced
     * 
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value) {
        header('' . $key . ': ' . $value . '', true);
        $this->vars[$key] = $value;
    }
}
