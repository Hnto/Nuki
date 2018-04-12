<?php
namespace Nuki\Handlers\Http\Output;

use Nuki\Exceptions\Base;
use Nuki\Handlers\Core\Assist;
use Nuki\Handlers\Http\Headers;
use Nuki\Handlers\Http\Server;
use Nuki\Models\IO\Output\Content;
use Nuki\Skeletons\Handlers\Renderer;

class Response {
  /**
   * Contains a renderer
   * Can be a template engine, echo class etc.
   * 
   * @var Renderer $renderer
   */
  private $renderer;
  
  /**
   * Contains the server handler
   * 
   * @var Server $serverHandler
   */
  private $serverHandler;
  
  /**
   * Contains the headers handler
   * 
   * @var Headers
   */
  private $headersHandler;
  
  /**
   * Construct by renderer
   * 
   * @param Renderer $renderer
   */
  public function __construct(\Nuki\Skeletons\Handlers\Renderer $renderer) {
    //Set renderer to use
    $this->renderer = $renderer;
    
    //Set header handler
    $this->headersHandler = new \Nuki\Handlers\Http\Headers();
    
    //Set server handler
    $this->serverHandler = new \Nuki\Handlers\Http\Server();
  }
  
  /**
   * Send output to use with template path optional
   * 
   * @param mixed $template
   */
  public function send($template = false) {
    if ($template !== false) {
      $this->setContent(new Content($template));
    }
      
    $this->renderer->render($template);
  }

  /**
   * Get the header handler
   * 
   * @return Headers
   */
  public function headers() {
    return $this->headersHandler;
  }
  
  /**
   * Get the server handler
   * 
   * @return Server
   */
  public function server() {
    return $this->serverHandler;
  }
  
  /**
   * Redirect to another page with additional delay
   * 
   * @param string $to
   * @param int $delay
   *
   * @return boolean
   */
  public function redirect($to, int $delay = 0) {
    $host = $this->server()->get('HTTP_HOST');
    
    $protocol = 'http';
    if ($this->server()->get('HTTPS')) {
      $protocol = 'https';
    }
    
    $domain = $protocol . '://' . $host;
    
    if ($delay !== 0) {
      header('Refresh: ' . $delay . '; url=' . $domain . '/' . $to);     
      return true;
    }
    
    header('Location: ' . $domain . '/' . $to);
    exit;
  }
  
  /**
   * Set http status code
   * 
   * @param int $code
   *
   * @return Response $response
   */
  public function httpStatusCode(int $code = 200) {
    http_response_code($code);

    return $this;
  }

  /**
   * Set content disposition
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function httpContentDisposition(string $value) {
    $this->headers()->add('Content-Disposition', $value);

    return $this;
  }
  
  /**
   * Set http header cache control
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function httpCacheControl(string $value) {
    $this->headers()->add('Cache-Control', $value);

    return $this;
  }
  
  /**
   * Set http header expiration
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function httpExpires(string $value) {
    $this->headers()->add('Expires', $value);

    return $this;
  }
  
  /**
   * Set http header content type
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function httpContentType(string $value) {
    $this->headers()->add('Content-type', $value);

    return $this;
  }
  
  /**
   * Set access control allow origin 
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function allowOrigin(string $value) {
    $this->headers()->add('Access-Control-Allow-Origin', $value);

    return $this;
  }
  
  /**
   * Set access control allow credentials 
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function allowCredentials(string $value) {
    $this->headers()->add('Access-Control-Allow-Credentials', $value);

    return $this;
  }
  
  /**
   * Set access control allow methods 
   * 
   * @param array $methods
   *
   * @return Response $response
   */
  public function allowMethods(array $methods = []) {
    $this->headers()->add('Access-Control-Allow-Methods', implode(', ', $methods));

    return $this;
  }

  /**
   * Set access control max age 
   * 
   * @param string $value
   *
   * @return Response $response
   */
  public function maxAge(string $value) {
    $this->headers()->add('Access-Control-Max-Age', $value);

    return $this;
  }

  /**
   * Set access control allow headers 
   * 
   * @param array $headers
   *
   * @return Response $response
   */
  public function allowHeaders(array $headers = []) {
    $this->headers()->add('Access-Control-Allow-Headers', implode(', ', $headers));

    return $this;
  }

  /**
   * Set content in renderer
   *
   * @param Content $content
   *
   * @return Response $response
   */
  public function setContent(Content $content) {
    $this->renderer()->setContent($content);

    return $this;
  }
  
  /**
   * Get content of the renderer
   * 
   * @return string
   */
  public function getContent() {
    return $this->renderer()->getContent()->get();
  }
  
  /**
   * Return the renderer
   * 
   * @return Renderer $renderer
   */
  public function renderer() {
    return $this->renderer;
  }
}
