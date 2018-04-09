<?php
namespace Nuki\Handlers\Http\Output\Renderers;

class RawRenderer implements \Nuki\Skeletons\Handlers\Renderer {

  /**
   * Contains content
   * 
   * @var \Nuki\Models\IO\Output\Content 
   */
  private $content;
  
  /**
   * {@inheritdoc}
   */ 
  public function render() {
    echo $this->getContent()->get();
  }

  /**
   * {@inheritdoc}
   */
  public function setup() {
  }

  /**
   * Get the renderer
   * 
   * @return RawRenderer
   */
  public function getRenderer() : RawRenderer {
    return $this;
  }

  /**
   * {@inheritdoc}
   */ 
  public function setContent(\Nuki\Models\IO\Output\Content $content) {
    $this->content = $content;
  }

  /**
   * {@inheritdoc}
   */ 
  public function getContent(): \Nuki\Models\IO\Output\Content {
    return $this->content;
  }
}
