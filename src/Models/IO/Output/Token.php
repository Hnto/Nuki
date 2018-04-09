<?php
namespace Nuki\Models\IO\Output;

final class Token implements \Nuki\Skeletons\Models\Model {
  
  private $token;
  
  /**
   * Generate token or set by own
   * 
   * @param bool|string $token
   * @return void
   */
  public function __construct($token = false) {
    if ($token !== false) {
      $this->token = $token;
      
      return;
    }
    
    $this->token = \Nuki\Handlers\Core\Assist::randomString(20);
  }
  
  /**
   * Get token
   * 
   * @return string
   */
  public function get() {
    return $this->token;
  }
  
  /**
   * Validate token
   * 
   * @param array $tokenInfo
   * @return bool
   */
  public function validate(array $tokenInfo) : bool {
    $token = isset($tokenInfo['token']) ? $tokenInfo['token'] : '';
    return ($this->token === $token);
  }
}
