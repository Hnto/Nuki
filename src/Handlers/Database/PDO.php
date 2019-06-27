<?php
namespace Nuki\Handlers\Database;

use Nuki\Providers\StorageConnector;
use Nuki\Skeletons\Handlers\StorageHandler;
use Nuki\Exceptions\Base;

class PDO implements StorageHandler {
    
    /**
     * Contains the current connector
     * 
     * @var StorageConnector
     */
    private $connector;
    
    /**
     * Set the connection
     * 
     * @param StorageConnector $connector
     * @throws Base
     */
    public function __construct(StorageConnector $connector) {
        if (empty($connector->getConnection())) {
            throw new Base('no connection is available');
        }
        
        $this->connector = $connector;
    }

    /**
     * Must return the current connector
     *
     * @return StorageConnector
     */
    public function getConnector(): StorageConnector
    {
        return $this->connector;
    }

    /**
     * Insert a record in the storage
     * 
     * @param string $query
     * @param array $data
     * @return string the last inserted id
     */
    public function insert($query, array $data){        
        $this->connector
            ->getConnection()
            ->prepare($query)->execute($data)
        ;

        return $this->connector
            ->getConnection()
            ->lastInsertId()
        ;
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
        $stmt = $this->connector
            ->getConnection()
            ->prepare($query)
        ;

        $stmt->execute($data);
        
        return $stmt;
    }
}
