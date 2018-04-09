<?php
namespace Nuki\Skeletons\Handlers;

use \Nuki\Models\IO\Output\Content;

interface Renderer {
  /**
   * Setup renderer specifications
   */
  public function setup();
  
  /**
   * Set content
   * 
   * @param Content $content
   */
  public function setContent(Content $content);
  
  /**
   * Return content
   * @return Content
   */
  public function getContent() : Content ;
  
  /**
   * Return renderer
   * @return object
   */
  public function getRenderer();
  
  /**
   * Render content
   */
  public function render();
}
