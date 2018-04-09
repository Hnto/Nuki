<?php
namespace Nuki\Skeletons\Handlers;

interface BaseHandler { 
    /**
     * Add a new var
     * 
     * @param mixed $key
     * @param mixed $value
     */
    public function add($key, $value);
    
    /**
     * Get a setted var
     * 
     * @param mixed key
     */
    public function get($key);
    
    /**
     * Remove an item
     * 
     * @param string $key
     */
    public function remove($key);
    
    /**
     * Checks whether the key exists.
     * Return true if it exists or false if not
     * 
     * @param bool $key
     */
    public function has($key) : bool;
}