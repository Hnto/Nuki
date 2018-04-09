<?php
namespace Nuki\Handlers\Http;

use Nuki\Skeletons\Handlers\BaseHandler;

class Get implements BaseHandler {

    /**
     * Contains get vars
     *
     * @var array
     */
    private $vars;

    /**
     * Init all headers vars
     */
    public function __construct() {
        $this->vars = $_GET;
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
        $getValue = filter_var($value, $filter, $options);
        $_GET[$key] = $getValue;
        $this->vars[$key] = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($key) {
        unset($_GET[$key]);
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
    
  /**
   * {@inheritdoc}
   */
  public function set($key, $value) {
    if ($this->has($key)) {
      $this->remove($key);
      $this->add($key, $value);
      
      return true;
    }
    
    $this->add($key, $value);
            
    return true;
  }
}
