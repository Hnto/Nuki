<?php
namespace Nuki\Handlers\Repository;

use Nuki\Application\Application;
use Nuki\Exceptions\Base;
use Nuki\Skeletons\Handlers\BaseHandler;

class RepositoryHandler implements BaseHandler {
  
  private $repositories = [];

  /**
   * {@inheritdoc}
   */
  public function add($key, $value) {
    $this->repositories[$key] = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function get($key) {
    if (!$this->has($key)) {
        return null;
    }

    return $this->repositories[$key];
  }

  /**
   * {@inheritdoc}
   */
  public function remove($key) {
    unset($this->repositories[$key]);
  } 

  /**
   * {@inheritdoc}
   */
  public function has($key): bool {
    if (!isset($this->repositories[$key])) {
        return false;
    }

    return true;
  }

  /**
   * Build a repository by its key and additional options
   * 
   * @param string $key
   *
   * @return \Nuki\Skeletons\Providers\Repository
   *
   * @throws Base
   */
  public function buildRepository(string $key) {
    $repositoryInfo = $this->get($key);

    if (is_null($repositoryInfo)) {
        throw new \Nuki\Exceptions\Base(vsprintf('%1$s is not a registered repository', [$key]));
    }

    $this->validate($repositoryInfo, $key);

    $repository = new $repositoryInfo['Location'];

    return $repository;
  }

  /**
   * Validate repository info
   * 
   * @param array $repository
   * @throws \Nuki\Exceptions\Base
   */
  private function validate(array $repository = [], $key) {
    if (!isset($repository['Providers']) || empty($repository['Providers'])) {
        throw new \Nuki\Exceptions\Base(vsprintf('%1$s does not have any providers', [$key]));
    }

    if (!class_exists($repository['Location'])) {
        throw new \Nuki\Exceptions\Base(vsprintf('%1$s cannot be found', [$repository['Location']]));
    }
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
