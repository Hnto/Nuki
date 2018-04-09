<?php
namespace Nuki\Models\IO\Input;

final class Encrypted implements \Nuki\Skeletons\Models\Model {
    
    /**
     * Contains encrypted data
     * 
     * @var string
     */
    private $encrypted;

    /**
     * Set encrypted data
     * 
     * @param string $data
     */
    public function __construct($data) {
       $this->encrypted = $data; 
    }
    
    /**
     * Return encrypted data
     * 
     * @return string
     */
    public function get() {
        return $this->encrypted;
    }
}
