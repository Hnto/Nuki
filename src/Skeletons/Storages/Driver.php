<?php
namespace Nuki\Skeletons\Storages;

interface Driver {
    /**
     * Constructor to set the driver options
     * 
     * @param array $options
     */
    public function __construct(array $options = array());

    /**
     * Get the driver name
     */
    public function getName();
    
    /**
     * Get the driver options
     */
    public function getOptions();
}
