<?php
namespace Nuki\Skeletons\Services;

use Nuki\Application\Application;
use Nuki\Exceptions\Base;

abstract class Service {
    
    /**
     * Constant which defines that the service execution produced an error
     */
    const SERVICE_STATUS_ERROR = 'error';
    
    /**
     * Constant which defines that the service execution has been succesful
     */
    const SERVICE_STATUS_SUCCESS = 'success';
    
    /**
     * Constant which defines that the service execution has produced an unknown result
     */
    const SERVICE_STATUS_UNKNOWN = 'unknown';

    /**
     * Constructor to search for provided events
     *
     * @param Application $app
     *
     * @throws Base
     */
    public function __construct(Application $app) {
        $servicesContent = file_get_contents(
            $app->getService('params-handler')->get('app-dir')->value() .
            Application::SERVICES_INFO_PATH
        );

        if ($servicesContent === false) {
            throw new Base('The services file has not been found');
        }

        //If events have been added, register them in the eventHandler
        $services = json_decode($servicesContent, true);
        if (!isset($services['Services'][$app->getActiveService()]['Events']) &&
            !is_array($services['Services'][$app->getActiveService()]['Events'])) {

            return;
        }

        $events = [];
        if (!empty($services['Services'][$app->getActiveService()]['Events'])) {
            $events = $services['Services'][$app->getActiveService()]['Events'];
        }

        $eventHandler = $app->getService('event-handler');

        foreach($events as $name => $event) {
            if (!class_exists($event)) {
                continue;
            }

            $caller = $app->getActiveUnit() .
                $app::APPLICATION_SERVICES_TRAIL .
                $app->getActiveService() . '::' .
                $app->getActiveProcess();

            $eventHandler->registerEvent($event, [
                    'name' => $name,
                    'caller' => $caller,
                ]
            );
        }
    }
}
