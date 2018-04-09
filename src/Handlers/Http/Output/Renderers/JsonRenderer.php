<?php
namespace Nuki\Handlers\Http\Output\Renderers;

class JsonRenderer implements \Nuki\Skeletons\Handlers\Renderer {

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
    $json = $this->createJson();
    
    echo $json;
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
  public function getRenderer() : JsonRenderer {
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
  
  /**
   * Check if json is valid
   * 
   * @param string $data
   * @return bool
   */
  public function isValidJson(string $data) : bool {
    json_decode($data);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
      return false;
    }
    
    return true;
  }
  
  /**
   * Create json object
   * 
   * @return string
   * @throws \Nuki\Exceptions\Base
   */
  private function createJson() {
    $content = $this->getContent()->get();
    $json = json_encode(
      $content,
      JSON_PRESERVE_ZERO_FRACTION |
      JSON_BIGINT_AS_STRING |
      JSON_NUMERIC_CHECK |
      JSON_PRETTY_PRINT |
      JSON_UNESCAPED_SLASHES
    );

    if (!$this->isValidJson($json)) {
      throw new \Nuki\Exceptions\Base('Encoded json string is not valid, check input');
    }
    
    return $json;
  }
}
