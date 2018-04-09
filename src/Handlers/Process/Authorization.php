<?php
namespace Nuki\Handlers\Process;

use Nuki\Models\IO\{
  Output\Token
};

class Authorization implements \Nuki\Skeletons\Handlers\Helper {

  /**
   * Contains a token object
   * 
   * @var \Nuki\Models\IO\Output\Token 
   */
  private $token;

  /**
   * Contains the input to verify against token
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

    $headers = array_change_key_case(
      array_merge(
        $request->headers()->get(),
        $request->post()->get()
      )
    );
    $token = $this->searchValue($options['ids']['token'], $headers);

    //Set token
    $this->token = new Token($token);
  }

  /**
   * Get the token
   * 
   * @return string|null
   */
  public function token() {
    return $this->token->get();
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
   * Verify if authorization is successful
   * 
   * @return bool
   */
  public function isSuccess() : bool {
    if (!$this->token->validate($this->input)) {
      return false;
    }
    
    return true;
  }
  
  /**
   * Search given value from ids of values
   * 
   * @param array $values
   * @param array $headers
   * @return mixed
   */
  private function searchValue(array $values = [], array $headers = []) {
    foreach($values as $value) {
      if (!array_key_exists($value, $headers)) {
        continue;
      }

      return $headers[$value];
    }
    
    return null;
  }  
}
