<?php
namespace Nuki\Handlers\Http\Output\Renderers;

class FoilRenderer implements \Nuki\Skeletons\Handlers\Renderer {

  /**
   * Contains content
   * 
   * @var \Nuki\Models\IO\Output\Content 
   */
  private $content;
  
  /**
   * Contains params
   * 
   * @var array 
   */
  private $params = [];
  
  /**
   * Contains foil engine
   * 
   * @var \Foil\Engine $engine 
   */
  private $engine;
  
  /**
   * {@inheritdoc}
   */ 
  public function render() {
    $template = $this->content->get();
    if (!$this->templateExists($template)) {
      throw new \Nuki\Exceptions\Base(vsprintf('Template {%1$s} not found', [$template]));
    }

    echo $this->engine->render($template, $this->params);
  }

  /**
   * {@inheritdoc}
   */
  public function setup(array $info = []) {
    $folders = $info['folders'];
    $options = $info['options'];
    
    $foil = \Foil\Foil::boot([
        'folders' => $folders,
        'autoescape' => isset($options['autoescape']) ? $options['autoescape'] : false,
        'alias' => isset($options['alias']) ? $options['alias'] : false,
    ]);

    $this->engine = $foil->engine();
  }
  
  /**
   * Check if template exists
   * 
   * @param string $template
   * @return boolean
   */
  public function templateExists($template) : bool {
    if (!$this->engine->find($template)) {
      return false;
    }

    return true;
  }
  
  /**
   * Get foil engine to use foil methods
   * 
   * @return \Foil\Engine
   */
  public function engine() : \Foil\Engine {
    return $this->engine;
  }

    /**
     * Add params
     * @param array $params
     */
    public function addParams(array $params = []) {
      $this->params = array_merge($this->params, $params);
    }

  /**
   * Get params
   * 
   * @return array
   */
  public function getParams() : array {
    return $this->params;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getContent(): \Nuki\Models\IO\Output\Content {
    return $this->content;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getRenderer() : FoilRenderer {
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function setContent(\Nuki\Models\IO\Output\Content $content) {
    $this->content = $content;
  }
}

