<?php
namespace Nuki\Skeletons\Providers;

use Nuki\Application\Application;
use Nuki\Handlers\Core\Assist;
use Zend\Hydrator;

abstract class Repository {

    private $data;

    /**
     * Contains providers
     * 
     * @var array 
     */
    private $providers = [];
    
    /**
     * Contains the hydrator
     * 
     * @var Hydrator\ClassMethods() 
     */
    private $hydrator;

    /**
     * Repository constructor.
     * All providers linked to the repository will be built
     * and set in $providers.
     * The provider key will be the first word of the provider
     * class before the first uppercase.
     * Example: UserProvider becomes user
     */
    public function __construct() {

        $container = Application::getContainer();

        $repository = Assist::classNameShort($this);

        if (!isset($container->offsetGet('repositories')[$repository])) {
            return false;
        }

        if (!isset($container->offsetGet('repositories')[$repository]['Providers'])) {
            return false;
        }

        $repositoryProviders = $container->offsetGet('repositories')[$repository]['Providers'];
        $providers = $container->offsetGet('providers');

        foreach($repositoryProviders as $repoProvider) {
            if (isset($providers[$repoProvider])) {
                $providerPieces = preg_split('/(?=[A-Z])/', $repoProvider);
                $providerPieces = array_map('strtolower', $providerPieces);

                $this->providers[$providerPieces[1]] = $container->offsetGet('provider-handler')->buildProvider(
                    $repoProvider,
                    ['storageHandler' => $container->offsetGet('storage-handler')]
                );

                $this->$repoProvider = $this->providers[$providerPieces[1]];
            }
        }
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function addProvider($key, $value) {
        if (isset($this->providers[$key])) {
            return false;
        }
        
        $this->providers = [
            $key => $value
        ];
    } 
    
    /**
     * Get providers
     * 
     * @return array
     */
    public function getProviders() {
        return $this->providers;
    }
    
    /**
     * Get provider by key
     * 
     * @param string $key
     * @return object
     */
    public function getProvider($key) {
        if (!isset($this->providers[$key])) {
            return null;
        }
        
        return $this->providers[$key];
    }

    /**
     * Get hydrator
     * 
     * @return \Zend\Hydrator\ClassMethods
     */
    public function getHydrator() : Hydrator\ClassMethods {
        if (!$this->hydrator instanceof Hydrator\ClassMethods) {
            $this->hydrator = new Hydrator\ClassMethods();
        }
        
        return $this->hydrator;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }
}
