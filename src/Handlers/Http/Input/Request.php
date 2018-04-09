<?php
namespace Nuki\Handlers\Http\Input;

use Nuki\Application\Application;
use Nuki\Exceptions\Base;
use Nuki\Handlers\Core\Assist;
use Nuki\Handlers\Http\{
    Get, Post, Cookie, Router, Server, Files, Headers
};
use Nuki\Handlers\Provider\Params;
use Nuki\Models\IO\Input\Param;
use Units\Authentication\Services\Login;
use Zend\Stdlib\Parameters;

class Request {
  /**
   * Contains the get handler
   * 
   * @var Get $getHandler
   */
  private $getHandler;
  
  /**
   * Contains the post handler
   * 
   * @var Post $postHandler
   */
  private $postHandler;
  
  /**
   * Contains the cookie handler
   * 
   * @var Cookie $cookieHandler
   */
  private $cookieHandler;
  
  /**
   * Contains the server handler
   * 
   * @var Server $serverHandler
   */
  private $serverHandler;

  /**
   * Contains the files handler
   * 
   * @var Files $filesHandler
   */
  private $filesHandler;
  
  /**
   * Contains the headers handler
   * 
   * @var Headers $headersHandler
   */
  private $headersHandler;

  /**
   * Constructor to set handlers
   */
    public function __construct() {
        $this->getHandler = new Get();
        $this->postHandler = new Post();   
        $this->cookieHandler = new Cookie();
        $this->serverHandler = new Server();
        $this->filesHandler = new Files();
        $this->headersHandler = new Headers();
    }

    /**
     * Returns both POST and GET values
     *
     * @return Parameters
     */
    public function all()
    {
        return new Parameters(
            array_merge(
                $this->post()->get(),
                $this->get()->get()
            )
        );
    }
    
    /**
     * Get the get handler
     * 
     * @return Get
     */
    public function get() {
        return $this->getHandler;
    }
  
    /**
     * Get the post handler
     * 
     * @return Post
     */
    public function post() {    
        return $this->postHandler;
    }
    
    /**
     * Get the cookie handler
     * 
     * @return Cookie
     */
    public function cookie() {
      return $this->cookieHandler;
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
     * Get the headers handler
     *
     * @return Headers
     */
    public function headers() {
      return $this->headersHandler;
    }
    
    /**
     * Return path without host or additional query strings
     * 
     * @return string
     */
    public function path() {
      return strtok($this->server()->get('REQUEST_URI'), '?');
    }
    
    /**
     * Return full URI path without host
     * 
     * @return string
     */
    public function queryPath() {
      return $this->server()->get('REQUEST_URI');
    }
    
    /**
     * Return host
     * 
     * @return string
     */
    public function httpHost() {
      return $this->server()->get('HTTP_HOST');
    }
    
    /**
     * Return request method
     * 
     * @return string
     */
    public function method() : string {
      return $this->server()->get('REQUEST_METHOD');
    }
    
    /**
     * Return domain
     * 
     * @return string
     */
    public function domain() {
      $host = $this->httpHost();
      $protocol = $this->protocol();

      $domain = $protocol . '://' . $host;

      return $domain;
    }
    
    /**
     * Get used server protocal
     * 
     * @param bool $version
     * @return string
     */
    public function protocol($version = false) : string {
      if ($version === false) {
        $protocol = 'http';
        if ($this->server()->get('HTTPS')) {
          $protocol = 'https';
        }
        
        return $protocol;
      }
      
      return $this->server()->get('SERVER_PROTOCOL');
    }
    
    /**
     * Get http accepted content types
     * 
     * @return array
     */
    public function httpAccept() {
      $accepted = $this->server()->get('HTTP_ACCEPT');
      
      return explode(',', $accepted);
    }
    
    /**
     * Get http cache control
     * 
     * @return string
     */
    public function httpCache() {
      return $this->server()->get('HTTP_CACHE_CONTROL');
    }
    
    /**
     * Get http connection
     * 
     * @return string
     */
    public function httpConnection() {
      return $this->server()->get('HTTP_CONNECTION');
    }
    
    /**
     * Get http cookie
     * 
     * @return string
     */
    public function httpCookie() {
      return $this->server()->get('HTTP_COOKIE');
    }    
    
    /**
     * Get http accepted encoding
     * 
     * @return array
     */
    public function httpAcceptEncoding() {
      $accepted = $this->server()->get('HTTP_ACCEPT_ENCODING');
      
      return explode(',', $accepted);
    }
    
    /**
     * Get http accepted languages
     * 
     * @return array
     */
    public function httpAcceptLanguage() {
      $accepted = $this->server()->get('HTTP_ACCEPT_LANGUAGE');
      
      return explode(',' , $accepted);
    }
    
    /**
     * Get http user agent
     * 
     * @return string
     */
    public function httpUserAgent() {
      return $this->server()->get('HTTP_USER_AGENT');
    }
    
    /**
     * Set access control request method
     * 
     * @param string $method
     */
    public function requestMethod(string $method) {
      $this->headers()->add('Access-Control-Request-Method', $method);
    }

    /**
     * Set access control request headers
     * 
     * @param array $headers
     */
    public function requestHeaders(array $headers = []) {
      $this->headers()->add('Access-Control-Request-Headers', implode(', ', $headers));
    }
    
    /**
     * Get all uploaded files
     * @return array
     */
    public function getUploadedFiles() {
      return $this->filesHandler->files();
    }
    
    /**
     * Move all uploaded files
     * 
     * @param string $destination
     */
    public function moveUploadedFiles($destination) {
      $files = $this->getUploadedFiles();
      
      foreach($files as $file) {
        
      }
    }

    /**
     * Initialize incoming request
     * Determine if route contains callback or
     * service execution
     *
     * - set callback
     *
     * - set active unit
     * - set active service
     * - set active process
     * - register service
     *
     * @param Application $app
     *
     * @return void
     *
     * @throws Base
     */
    public function incoming(Application $app) {

        $route = $app->getService('router')
            ->findHttpRoute($this);

        /** Route is a callback execution */
        if (!empty($route->offsetGet('action'))) {
            //Route is a callback execution
            $app::getContainer()->offsetSet(
                'callback',
                $route
            );
	    
	    return;
        }

        /** Route is a service execution */
        //Set active unit
        $app->setActiveUnit(
            Application::APPLICATION_UNIT_NAMESPACE . $route->get('unit')
        );

        //Set active service
        if (!class_exists(
            $app->getActiveUnit() .
            Application::APPLICATION_SERVICES_TRAIL .
            $route->get('service'))
        ) {
            throw new \Nuki\Exceptions\Base('No available service has been found for the provided unit');
        }
        $app->setActiveService($route->get('service'));

        //Set active process
        $process = $this->buildProcess(
            $route->get('process'),
            $app->getActiveUnit() . Application::APPLICATION_SERVICES_TRAIL . $app->getActiveService()
        );
        $app->setActiveProcess($process);

        //Add params from service call to params-handler
        /** @var Params $paramsHandler */
        $paramsHandler = $app->getService('params-handler');

        $paramsHandler->add('route-params', $route->get('params'));
    }

    /**
     * Build process name
     *
     * @param string $process
     * @param string $fullServiceName
     * @return string
     * @throws \Nuki\Exceptions\Base
     */
    private function buildProcess(string $process, string $fullServiceName) {
      $methods = array_flip(get_class_methods($fullServiceName));

      if (!array_key_exists(($process), $methods)) {

        throw new \Nuki\Exceptions\Base('No available process is found for the provided service');
      }
      
      return $process;
    }
}
