<?php
namespace Nuki\Models\IO\Input;

final class Credentials {
  
  /**
   * Contains the username
   *
   * @var string
   */
  private $username;
  
  /**
   * Contains the password
   * 
   * @var string 
   */
  private $password;
  
  /**
   * Set username and password
   * 
   * @param string $username
   * @param string $password
   */
  public function __construct(string $username, string $password) {
    $this->username = $username;
    $this->password = $password;
  }

    /**
   * Get username
   * 
   * @return string
   */
  public function getUsername() : string {
    return $this->username;
  }
  
  /**
   * Get encrypted password
   * 
   * @return string
   */
  public function getPassword() : string {
    return $this->password;
  }
  
  /**
   * Validate credentials
   * 
   * @param array $creds
   * @return bool
   */
  public function validate(array $creds = []) : bool {
    $user = isset($creds['user']) ? $creds['user'] : false;
    $pass = isset($creds['password']) ? $creds['password'] : false;

    $decrypted = Assist::hash(Assist::decrypt($this->password));

    if ($this->username === $user && $decrypted === $pass) {
      return true;
    }
    
    return false;
  }
}
