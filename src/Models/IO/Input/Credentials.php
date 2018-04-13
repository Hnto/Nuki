<?php

namespace Nuki\Models\IO\Input;

use Nuki\Handlers\Core\Assist;

final class Credentials {

    const USER_KEYS = [
        'user', 'username', 'x-username', 'x-user'
    ];

    const PASSWORD_KEYS = [
        'pass', 'password', 'x-password', 'x-pass'
    ];

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
   * @param array $creds [user,username,x-user,x-username] [pass, password, x-pass, x-password]
   *
   * @return bool
   */
  public function validate(array $creds = []) : bool {
      $user = '';
      $pass = '';

      //Check for user keys
      foreach (self::USER_KEYS as $key) {
          if (isset($creds[$key])) {
              $user = $creds[$key];
          }
      }

      //Check for password keys
      foreach (self::PASSWORD_KEYS as $key) {
          if (isset($creds[$key])) {
              $pass = $creds[$key];
          }
      }

      $decrypted = Assist::hash(Assist::decrypt($this->password));

      if ($this->username === $user && $decrypted === $pass) {
          return true;
      }

      return false;
  }
}

