<?php
namespace Nuki\Handlers\Http;

class Files {

  /**
   * Contains the files
   * 
   * @var array 
   */
  private $files = [];
  
  /**
   * Setup files
   * 
   */
  public function __construct() {
    if (empty($_FILES)) {
      return;
    }
    
    $this->files = $this->normalize();
  }
  
  /**
   * Get file by key
   * 
   * @param int $key
   * @return \Nuki\Models\Data\File
   */
  public function file($key) {
    if (!isset($this->files[$key])) {
      return null;
    }
    
    return $this->createFile($this->files[$key]);
  }

  /**
   * Get all uploaded files
   * 
   * @return array
   */
  public function files() {
    return $this->files;
  }
  
  /**
   * Move file
   */
  public function move($key) {
    
  }
  
  /**
   * Create file object
   * 
   * @param array $fileInfo
   * @return \Nuki\Models\Data\File
   */
  private function createFile(array $fileInfo = []) {
    $file = new \Nuki\Models\Data\File($fileInfo['tmp_name'], $fileInfo['size']);
    
    $file->setExtension(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
    $file->setName(pathinfo($fileInfo['name'], PATHINFO_BASENAME));
    
    return $file;
  }
  
  /**
   * Normalize $_FILES array
   * 
   * @return array
   */
  private function normalize() : array {
    $files = [];

    foreach($_FILES as $key => $file) {
      
      if (!is_array($file['name'])) {
          $files[$key][] = $file;
          continue;
      }

      foreach($file['name'] as $subKey => $name) {
          $files[$key][$subKey] = [
              'name' => $name,
              'type' => $file['type'][$subKey],
              'tmp_name' => $file['tmp_name'][$subKey],
              'error' => $file['error'][$subKey],
              'size' => $file['size'][$subKey]
          ];
      }
    }

    return $files;
  }
}
