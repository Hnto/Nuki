<?php
namespace Nuki\Handlers\Process;

use Nuki\Handlers\Core\Assist;
use Nuki\Models\IO\{
  Input\Credentials,
  Output\Token
};

class Authentication implements \Nuki\Skeletons\Handlers\Helper {

  /**
   * Contains a credentials object
   * 
   * @var \Nuki\Models\IO\Input\Credentials 
   */
  private $credentials;

  /**
   * Contains the input to verify against credentials
   * 
   * @var array $input
   */
  private $input;

  /**
   * Set helper specified options
   * 
   * @param array $options
   */
  public function __construct(array $options = array()) {
    $request = $options['request'];
    
    $requestInputs = array_change_key_case(
      array_merge(
        $request->post()->get(), 
        $request->headers()->get()
      )
    );
    $username = $this->searchValue($options['ids']['username'], $requestInputs);
    $password = $this->searchValue($options['ids']['password'], $requestInputs);

    if (!empty($password)) {
      $encrypted = Assist::encrypt($password);
      $password = $encrypted->get();
    }
    
    //Set credentials
    $this->credentials = new Credentials((string) $username, (string) $password);
  }

  /**
   * Get the username
   * 
   * @return string|null
   */
  public function username() {
    return $this->credentials->getUsername();
  }

  /**
   * Get the encrypted password
   * 
   * @return string|null
   */
  public function password() {
    return $this->credentials->getPassword();
  }

  /**
   * Set input data
   * 
   * @param array $input
   */
  public function setInput(array $input = []) {
    $this->input = $input;
  }
  
  /**
   * Verify if authentication is successful
   * 
   * @return bool|Token
   */
  public function isSuccess() {
    if (!$this->credentials->validate($this->input)) {
      return false;
    }
    
    return new Token();
  }
  
  /**
   * Search given value from ids of values
   * 
   * @param array $values
   * @param array $requestInputs
   * @return mixed
   */
  private function searchValue(array $values = [], array $requestInputs = []) {
    foreach($values as $value) {
      if (!array_key_exists($value, $requestInputs)) {
        continue;
      }

      return $requestInputs[$value];
    }
    
    return null;
  }  
}
