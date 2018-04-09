<?php
namespace Nuki\Models\IO\Output;

final class Content implements \Nuki\Skeletons\Models\Model {
  
  private $content;
  
  /**
   * Set content
   * 
   * @param mixed $content
   */
  public function __construct($content) {
    $this->content = $content;
  }
  
  /**
   * Get content
   * 
   * @return mixed
   */
  public function get() {
    return $this->content;
  }
}
