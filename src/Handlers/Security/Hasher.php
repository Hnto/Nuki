<?php
namespace Nuki\Handlers\Security;

class Hasher {
  
  /**
   * Contains the algorithm to use
   * 
   * @var string 
   */
  private $algorithm;
  
  /**
   * Contains the hmac key
   * 
   * @var string 
   */
  private $key;

  /**
   * Setup hasher by algorithm and key
   * 
   * @param string $algorithm
   * @param string $key
   * @return boolean
   */
  public function __construct(string $algorithm, string $key) {
      $algos = array_flip($this->algorithms());
      if (!array_key_exists($algorithm, $algos)) {
          return false;
      }

      $this->algorithm = $algorithm;
      $this->key = $key;
  }

  /**
   * Hash by message
   * 
   * @param string $data
   * @return bool|string
   */
  public function hash(string $data, $file = false) {
    if ($file === false) {
      return $this->hashContent($data);
    }
    
    return $this->hashFile($file);
  }
  
  /**
   * Verify two hashes
   * 
   * @param string $hashed
   * @param string $hash
   *
   * @return bool
   */
  public function verify($hashed, $hash) {
      return hash_equals($hashed, $hash);   
  }

  /**
   * Return all available algorithms
   * 
   * @return array
   */
  public function algorithms() {
      return hash_algos();
  }
  

  /**
   * Hash by filename
   * 
   * @param string $filename
   * @return bool|string
   */
  private function hashFile(string $filename) {
    $hash = hash_hmac_file(
      $this->algorithm,
      $filename,
      $this->key
    );

    return $this->validate($hash);
  }
  
  /**
   * Hash by content
   * 
   * @param string $data
   * @return boolean
   */
  private function hashContent(string $data) {
    $hash = hash_hmac(
      $this->algorithm,
      $data,
      $this->key
    );

    return $this->validate($hash);
  }
  
  /**
   * Validate hash
   * 
   * @param string|bool $hash
   * @return string|bool
   */
  private function validate($hash) {
    if ($hash === false) {
      return false;
    }

    return $hash;
  }  
}
