<?php
namespace Nuki\Handlers\Database;

use Nuki\Providers\StorageConnector;
use Nuki\Skeletons\Handlers\StorageHandler;
use Nuki\Exceptions\Base;

class PDO implements StorageHandler {
    
    /**
     * Contains the current connector
     * 
     * @var object 
     */
    private $conn;
    
    /**
     * Set the connection
     * 
     * @param StorageConnector $connector
     * @throws Base
     */
    public function __construct(StorageConnector $connector) {
        $connection = $connector->getConnection();
        
        if (empty($connection)) {
            throw new Base('no connection is available');
        }
        
        $this->conn = $connection;
    }
    
    /**
     * Insert a record in the storage
     * 
     * @param string $query
     * @param array $data
     * @return string the last inserted id
     */
    public function insert($query, array $data){        
        $this->conn->prepare($query)->execute($data);
        
        return $this->conn->lastInsertId();
    }

    /**
     * Update an existing record in the storage
     * 
     * @param string $query
     * @param array $data
     * @return int the number of rows
     */
    public function update($query, array $data) {
        $stmt = $this->executeQuery($query, $data);

        return $stmt->rowCount();       
    }

    /**
     * Delete a record in the storage
     * 
     * @param string $query
     * @param array $data
     * @return int the number of rows
     */
    public function delete($query, array $data) {
        $stmt = $this->executeQuery($query, $data);
        
        return $stmt->rowCount();       
    }

    /**
     * Fetch a record from the storage
     * 
     * @param string $query
     * @param array $data
     * @return object data as object
     */
    public function findOne($query, array $data = null) {       
        $stmt = $this->executeQuery($query, $data);   
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all records from a certain location in the storage
     * 
     * @param string $query
     * @param array $data
     * @return object as object
     */
    public function findMany($query, array $data = null){
        $stmt = $this->executeQuery($query, $data);
        
        return($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Execute a raw query
     * 
     * @param string $query
     * @param array $data
     * @return object statement object
     */
    private function executeQuery($query, $data = null) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($data);
        
        return $stmt;
    }
}
