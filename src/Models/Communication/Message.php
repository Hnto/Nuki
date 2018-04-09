<?php
namespace Nuki\Models\Communication;

use \Nuki\Models\IO\{
    Input\Encrypted
};

final class Message {
  /**
   * Contains an encrypted message
   * 
   * @var string 
   */
  private $message;

  /**
   * Construct by encrypted object
   * 
   * @param Encrypted $encrypted
   */
  public function __construct(Encrypted $encrypted) {      
      $this->message = $encrypted->get();
  }

  /**
   * Return encrypted message
   * 
   * @return mixed
   */
  public function getMessage() {
    return $this->message;
  }
}
