<?php
namespace Nuki\Providers;

use Nuki\Skeletons\Storages\Driver;

class StorageConnector {
    /**
     * Contains the connection
     * 
     * @var object 
     */
    private $conn;


    /**
     * StorageConnector constructor.
     * @param Driver $driver
     * @throws \Nuki\Exceptions\Base
     */
    public function __construct(Driver $driver) {
        $driverName = $driver->getName();

        if (empty($driverName)) {
            throw new \Nuki\Exceptions\Base('driver name is empty');
        }
        
        switch($driverName) {
            case "pdo":
                try {
                    $this->conn = new \PDO(
                        $driver->getOptions()['dsn'], 
                        $driver->getOptions()['user'], 
                        $driver->getOptions()['pass'], 
                        $driver->getOptions()['options']
                    );
                } catch (\PDOException $e) {
                    echo $e->getMessage();
                    return;
                }
                break;
        }
    }
    
    /**
     * Get the active connection
     * 
     * @return object
     */
    public function getConnection() {
        return $this->conn;
    }
}
