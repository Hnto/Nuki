<?php
namespace Nuki\Skeletons\Actors;

use Nuki\Handlers\Core\Assist;
use Nuki\Skeletons\Processes\Event;

abstract class Watcher {
    /**
     * Update method called by Event
     * This method can contain any sort of logic you'd want to
     * be executed when the Event notifies the watcher.
     * 
     * @param Event $event
     */
    abstract public function update(Event $event);
    
    /**
     * Get the name of the watcher
     * 
     * @return string
     */
    public function getName() {        
        return Assist::classNameShort($this);
    }
}
