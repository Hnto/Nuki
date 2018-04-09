<?php
namespace Nuki\Drivers;

use Nuki\Skeletons\Storages\Driver;

class PDO implements Driver {
    /**
     * Contains the driver options
     * 
     * @var array 
     */
    private $options = array(
        'dsn' => false, 
        'host' => false, 
        'user' => false,
        'pass' => false,
        'database' => false,
        'options' => array(),
    );
    
    /**
     * Contains the driver name
     * 
     * @var string 
     */
    private $name;
    
    /**
     * Set the driver options
     * 
     * @param array $options
     */
    public function __construct(array $options = array()) {
        $this->options['dsn'] = isset($options['dsn']) ? $options['dsn'] : false;
        
        $this->options['host'] = isset($options['host']) ? $options['host'] : false;
        
        $this->options['user'] = isset($options['user']) ? $options['user'] : false;
        
        $this->options['pass'] = isset($options['pass']) ? $options['pass'] : false;
        
        $this->options['database'] = isset($options['database']) ? $options['database'] : false;
        
        $this->options['options'] = isset($options['options']) && is_array($options['options']) ? $options['options'] : array();
        
        $this->compileAndSetName();
    }

    /**
     * Get the driver name
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the driver options
     * 
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }
    
    /**
     * Private function to compile and set the driver name
     */
    private function compileAndSetName() { 
      $name = \Nuki\Handlers\Core\Assist::classNameShort($this);
      
      $this->name = strtolower($name);
    }
}
