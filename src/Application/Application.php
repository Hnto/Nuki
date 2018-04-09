<?php
namespace Nuki\Application;

use Nuki\Exceptions\Base;
use Nuki\Handlers\Core\Resolver;
use Nuki\Handlers\Http\Input\Request;
use Nuki\Handlers\Http\Output\Response;
use Nuki\Models\IO\Output\Content;
use Nuki\Skeletons\Providers\Extender;
use Pimple\Container;

class Application {
    /**
     * Define default events location
     * Becomes: Nuki\Units\UNITNAME\Events\
     */
    const EVENTS_DEFAULT_LOCATION = '\\Events\\';

    /**
     * Define default events location
     * Becomes: Nuki\Units\UNITNAME\Watchers\
     */
    const WATCHERS_DEFAULT_LOCATION = '\\Watchers\\';

    /**
     * Define application root namespace
     * Becomes: Nuki\\ 
     */
    const APPLICATION_ROOT_NAMESPACE = '';

    /**
     * Define application unit namespace
     * Becomes: Nuki\\Units\\
     */
    const APPLICATION_UNIT_NAMESPACE = 'Units\\';

    /**
     * Define application services path trail
     * Becomes: \\Services\\
     */
    const APPLICATION_SERVICES_TRAIL = '\\Services\\';

    /**
     * Define storage connection info path
     * Example: see value
     */
    const STORAGE_CONNECTION_INFO_PATH = '/settings/Database/connection-pdo.json';

    /**
     * Define rendering info path
     * Example: see value
     */
    const RENDERING_INFO_PATH = '/settings/Rendering/rendering.json';

    /**
     * Define repositories info path
     * Example: see value
     */
    const REPOSITORIES_INFO_PATH = '/settings/Repositories/repositories.json';
    
    /**
     * Define providers info path
     * Example: see value
     */
    const PROVIDERS_INFO_PATH = '/settings/Providers/providers.json';

    /**
     * Define extenders info path
     * Example: see value
     */
    const EXTENDERS_INFO_PATH = '/settings/Extenders/extenders.json';

    /**
     * Define services info path
     * Example: see value 
     */
    const SERVICES_INFO_PATH = '/settings/Services/services.json';

    /**
     * Define application properties path
     * Example: see value
     */
    const APPLICATION_PROPS_PATH = '/settings/Application/app.properties';

    /**
    * Contains the container
    * 
    * @var \Pimple\Container
    */
    private static $container;

    /**
    * Contains the active unit to run
    * 
    * @var string 
    */
    private $activeUnit;

    /**
    * Contains the active service to run 
    * 
    * @var string 
    */
    private $activeService;

    /**
    * Contains the active process to run
    * 
    * @var string 
    */
    private $activeProcess;
    
    /**
     * Set the pimple container
     * 
     * @param \Pimple\Container $container
     */
    public function __construct(\Pimple\Container $container) {

        static::$container = $container;
    }

    /**
     * Retrieve an instance of the container
     *
     * @return Container
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * Register a service
     * 
     * @param object $service
     * @param string $name
     */
    public function registerService($service, $name = '') {
        if (empty($name)) {
            $name = \Nuki\Handlers\Core\Assist::classNameShort($service);
        }

        static::$container->offsetSet(strtolower($name), $service);
    }

    /**
     * Get a service
     * 
     * @param string $serviceName
     * @return object|null
     */
    public function getService($serviceName) {
        if (static::$container->offsetExists($serviceName) === false) {
            return null;
        }

        return static::$container->offsetGet($serviceName);
    }

    /**
     * Unregister a registered service
     * 
     * @param string $service
     */
    public function unregisterService($service) {
        static::$container->offsetUnset($service);
    }

    /**
     * Get the registered service names
     * 
     * @return array
     */
    public function getRegisteredServices() {
        return static::$container->keys();
    }

    /**
     * Get the active unit
     * 
     * @return string
     */
    public function getActiveUnit() {
        return $this->activeUnit;
    }

    /**
     * Set the active unit to run
     * 
     * @param string $unit
     */
    public function setActiveUnit($unit) {
        static::$container->offsetSet('active-unit', $unit);
        $this->activeUnit = $unit;
    }

    /**
     * Get the active service
     * 
     * @return string
     */
    public function getActiveService() {
        return $this->activeService;
    }

    /**
     * Set the active service to run
     * 
     * @param string $service
     */
    public function setActiveService($service) {
        static::$container->offsetSet('active-service', $service);
        $this->activeService = $service;
    }

    /**
     * Get the active process
     * 
     * @return string
     */
    public function getActiveProcess() {
        return $this->activeProcess;
    }

    /**
     * Set the active process to run
     * 
     * @param string $process
     */
    public function setActiveProcess($process) {
        static::$container->offsetSet('active-process', $process);
        $this->activeProcess = $process;
    }

    /**
     * Run the application
     * - Incoming request will be handled
     * - Registration in the application will be done
     * - Session will be started
     * - Service will be executed
     *
     * @return mixed
     */
    public function run()
    {
        /** @var Request $request */
        $request = $this->getService('request-handler');

        //Process incoming request
        try {
            $request->incoming($this);
        } catch (Base $exception) {
            /** @var Response $response */
            $this->getService('response-handler')
                ->setContent(
                    new Content($exception->getMessage())
                )->send();

            return;
        }

        //Register helper services
        $helpers = static::$container['helpers'];

        foreach($helpers as $helper => $options) {
            $this->registerService(new $helper($options));
        }

        //Start session
        $this->getService('session-handler')->start();

        if ($this->getService('router')->routeIsCallable()) {

            $this->executeCallback();

            return;
        }

        //Execute extenders
        $this->executeUnitExtenders($this->getActiveUnit());

        $this->executeService();
    }

    /**
     * Execute unit extenders
     *
     * @param string $unit
     */
    public function executeUnitExtenders(string $unit)
    {
        $extenders = $this->getService('unit-extenders');

        if (!is_array($extenders)) {
            return;
        }

        foreach ($extenders as $extender) {
            if (!class_exists($extender)) {
                continue;
            }

            /** @var Resolver $resolver */
            $resolver = $this->getService('resolver');

            try {
                $resolver->resolve(
                    $extender,
                    Extender::EXECUTE_METHOD,
                    $this
                );
            } catch (Base $e) {
                $this->getService('response-handler')
                    ->setContent(new Content($e->getMessage()))
                    ->send();
            }
        }
    }

    /**
     * Execute service
     */
    public function executeService()
    {
        //Add route to last used route
        $this->getService('router')
            ->setLastRouteUsed(
                $this->getService('request-handler')->queryPath()
            );

        /** @var Resolver $resolver */
        $resolver = $this->getService('resolver');

        //Execute service and process
        try {
            $resolver->resolve(
                $this->getActiveUnit() . self::APPLICATION_SERVICES_TRAIL . $this->getActiveService(),
                $this->getActiveProcess(),
                $this
            );
        } catch (Base $e) {
            $this->getService('response-handler')
                ->setContent(new Content($e->getMessage()))
                ->send();
        }
    }

    /**
     * Execute callback
     */
    public function executeCallback()
    {
        call_user_func_array(
            $this::getContainer()
                ->offsetGet('callback')
                ->get('action'),
            $this::getContainer()
                ->offsetGet('callback')
                ->get('params')
        );
    }
}
