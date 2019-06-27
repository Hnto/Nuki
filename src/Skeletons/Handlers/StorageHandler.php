<?php
namespace Nuki\Skeletons\Handlers;

use Nuki\Providers\StorageConnector;

interface StorageHandler {
    /**
     * Constructor to set the storage connector
     * 
     * @param StorageConnector $connector
     */
    public function __construct(StorageConnector $connector);

    /**
     * Must return the current connector
     *
     * @return StorageConnector
     */
    public function getConnector(): StorageConnector;
}
