<?php
namespace Nuki\Handlers\Provider;

use Nuki\Skeletons\Handlers\BaseHandler;

class ProviderHandler implements BaseHandler {
  
  private $providers = [];

  /**
   * {@inheritdoc}
   */
  public function add($key, $value) {
    $this->providers[$key] = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function get($key) {
    if (!$this->has($key)) {
        return null;
    }

    return $this->providers[$key];
  }

  /**
   * {@inheritdoc}
   */
  public function remove($key) {
    unset($this->providers[$key]);
  }

  /**
   * Build a provider by its key and additional options

   * @param string $key
   * @param array $options
   * @return \Nuki\Skeletons\Providers\Storage
   * 
   * @throws \Nuki\Exceptions\Base
   */
  public function buildProvider(string $key, array $options = []) {
    $provider = $this->get($key);

    switch($provider['Type']) {
      case "Database":
          if (!isset($options['storageHandler']) 
              || !$options['storageHandler'] instanceof \Nuki\Skeletons\Handlers\StorageHandler) {
              throw new \Nuki\Exceptions\Base('No storage handler provided');
          }

          return new $provider['Location']($options['storageHandler']);
      default:
        throw new \Nuki\Exceptions\Base('Unknown provider type given for provider ' . $provider['Location']);
    }   
  }

  /**
   * {@inheritdoc}
   */
  public function has($key): bool {
    if (!isset($this->providers[$key])) {
        return false;
    }

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $value) {
    if ($this->has($key)) {
      $this->remove($key);
      $this->add($key, $value);
      
      return true;
    }
    
    $this->add($key, $value);
            
    return true;
  }
}
