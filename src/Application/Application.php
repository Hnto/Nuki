<?php
namespace Nuki\Application;

use Nuki\Exceptions\Base;
use Nuki\Handlers\Core\{
    Assist,
    Resolver
};
use Nuki\Handlers\Events\TerminateApplication;
use Nuki\Handlers\Process\EventHandler;
use Nuki\Handlers\Http\{
    Input\Request,
    Output\Response
};

use Nuki\Handlers\Watchers\{
    ExitApplication,
    LogBeforeTerminate
};
use Nuki\Models\IO\Output\Content;
use Nuki\Skeletons\Providers\Extender;
use Pimple\Container;

class Application {

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
     * Setup the application
     *
     * - Set the storage driver
     * - Register default service providers
     * - Set helper parameters
     * - Register framework events
     *
     */
    public function __construct() {

        static::$container = new Container();

        /**
         * Set the storage driver
         */
        static::getContainer()->offsetSet('storage-driver', Assist::getAppStorageDriver());

        /**
         * Register service provider
         * with default libraries
         */
        self::getContainer()->register(new \Nuki\Providers\Core\Storage\Base());
        self::getContainer()->register(new \Nuki\Providers\Core\Input\Base());
        self::getContainer()->register(new \Nuki\Providers\Core\Output\Base());
        self::getContainer()->register(new \Nuki\Providers\Core\Helpers\Base());
        self::getContainer()->register(new \Nuki\Providers\Core\Framework\Base());

        /**
         * Set helper parameters
         */
        $this->getService('params-handler')->add('app-dir', dirname(getcwd()));

        //Register framework events
        $this->registerFrameworkEvents();
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
     * Registers the framework events
     */
    public function registerFrameworkEvents()
    {
        //Register terminate application event
        $this->getService('event-handler')
            ->registerEvent(
                TerminateApplication::class,
                [
                    'caller' => Assist::className($this),
                    'name' => TerminateApplication::class
                ]
            );
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

        //Execute extenders
        $this->executeUnitExtenders($this->getActiveUnit());

        //Execute callback
        if ($this->getService('router')->routeIsCallable()) {
            $this->executeCallback();
            return;
        }

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

    /**
     * Fire TerminateApplication event
     * with two framework watchers.
     * One to log and one to actually exit.
     */
    public function terminate()
    {
        /** @var EventHandler $eventHandler */
        $eventHandler = $this->getService('event-handler');

        $eventHandler->getEvent(TerminateApplication::class)->attach(new LogBeforeTerminate());
        $eventHandler->getEvent(TerminateApplication::class)->attach(new ExitApplication());

        $eventHandler->fire(TerminateApplication::class, ['app' => $this]);
    }
}

