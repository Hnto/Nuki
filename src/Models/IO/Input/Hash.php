<?php
namespace Nuki\Models\IO\Input;

final class Hash implements \Nuki\Skeletons\Models\Model {

  /**
   * Contains the hash
   * 
   * @var string 
   */
  private $hash;
  
  /**
   * Set hash
   * 
   * @param string $hash
   */
  public function __construct(string $hash) {
      $this->hash = $hash;
  }

  /**
   * Get hash
   * 
   * @return string
   */
  public function get() {
      return $this->hash;
  }
}
