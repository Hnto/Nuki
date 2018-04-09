<?php
namespace Nuki\Skeletons\Processes;

use \Nuki\Skeletons\{
    Actors\Watcher
};

abstract class Event {
    
    const EVENT_STATUS_SUCCESS = 'success';
    
    const EVENT_STATUS_FAILED = 'failed';

    private $stopNotify = false;

    /**
     * Contains watchers
     * 
     * @var array 
     */
    private $watchers = [];
    
    /**
     * Contains the event params
     * 
     * @var array 
     */
    private $params = [];
    
    /**
     * Contains the caller of this event
     * 
     * @var string
     */
    private $caller;
    
    /**
     * Contains the event status
     * 
     * @var string 
     */
    private $status;
    
    /**
     * Set the caller requesting the event
     * 
     * @param string $caller
     */
    public function __construct(string $caller) {
        $this->caller = $caller;
    }

    /**
     * Attach a watcher
     * 
     * @param Watcher $watcher
     */
    public function attach(Watcher $watcher) {
        $this->watchers[$watcher->getName()] = $watcher;
    }
    
    /**
     * 
     * @param Watcher $watcher
     */
    public function detach(Watcher $watcher) {
        unset($this->watchers[$watcher->getName()]);
    }

    /**
     * Get event watchers
     *
     * @return array
     */
    public function getWatchers() : array {
        return $this->watchers;
    }
    
    /**
     * Get event params
     * 
     * @return array
     */
    public function getParams() : array {
        return $this->params;
    }

    /**
     * Notify all available watchers
     */
    public function notify() {
        foreach($this->watchers as $watcher) {
          if ($this->stopNotify === true) {
              break;
          }

          $watcher->update($this);
        }
    }

    /**
     * Stop notifying any more watchers
     */
    public function stopNotifying()
    {
        $this->stopNotify = true;
    }

    /**
     * Get the caller of the event
     * 
     * @return $caller string
     */
    public function getCaller() : string {
        return $this->caller;
    }

    /**
     * Get the status of the event
     * 
     * @return string
     */
    public function getStatus() : string {
        return $this->status;
    }
    
    /**
     * Set the status of the event
     * 
     * @param string $status
     */
    public function setStatus(string $status) {
        $this->status = $status;
    }

    /**
     * Set event params
     * 
     * @param array $params
     */
    public function setParams(array $params = []) {
        foreach($params as $key => $value) {
            $this->params[$key] = $value;
        }
    }

    public function deleteParam($key)
    {
        unset($this->params[$key]);
    }
}
