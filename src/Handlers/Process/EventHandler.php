<?php
namespace Nuki\Handlers\Process;

use Nuki\{
    Skeletons\Processes\Event
};

class EventHandler {
    
    /**
     * Constant which defines a registered event
     * A registered event is available for modification
     */
    const EVENT_REGISTERED = 'registered';

    /**
     * Constant which defines a started event
     */
    const EVENT_STARTED = 'started';
    
    /**
     * Contains the registered events
     * 
     * @var array 
     */
    private $registeredEvents = [];

    /**
     * Register an event
     * 
     * @param string $event
     * @param array $params
     * 
     * @return boolean
     */
    public function registerEvent($event, array $params = []) : bool {
        if (isset($this->registeredEvents[$event])) {
            return false;
        }

        $registeredEvent = new $event($params['caller']);

        $this->registeredEvents[$params['name']] = $registeredEvent;
        
        $registeredEvent->setStatus(self::EVENT_REGISTERED);

        return true;
    }
    
    /**
     * Get a (registered) event
     * 
     * @param string $event
     * @return Event|null
     */
    public function getEvent($event) : Event {
        return isset($this->registeredEvents[$event]) ? $this->registeredEvents[$event] : null;
    }

    /**
     * Get all the events
     * 
     * @return array
     */
    public function getEvents() : array {
        return $this->registeredEvents;
    }

    /**
     * @param string $event
     * @param array $params
     *
     * @throws \Nuki\Exceptions\Base
     */
    public function fire(string $event, array $params = []) {
        $activeEvent = $this->registeredEvents[$event];

        if (!$activeEvent instanceof Event) {
            throw new \Nuki\Exceptions\Base('The active registered event is not a valid event');
        }

        $activeEvent->setParams($params);
        $activeEvent->process();
        $activeEvent->setStatus(self::EVENT_STARTED);

        $activeEvent->notify();
    }

    /**
     * Clear the event
     * 
     * @param string $eventName
     */
    public function clear($eventName) {
        $this->registeredEvents[$eventName] = null;
    }
}
