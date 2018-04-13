<?php

namespace Nuki\Handlers\Watchers;

use Nuki\Skeletons\Actors\Watcher;
use Nuki\Skeletons\Processes\Event;

class ExitApplication extends Watcher
{

    /**
     * Update method called by Event
     * This method can contain any sort of logic you'd want to
     * be executed when the Event notifies the watcher.
     *
     * @param Event $event
     */
    public function update(Event $event)
    {
        //Exit the application
        exit;
    }
}
