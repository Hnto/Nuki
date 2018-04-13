<?php

namespace Nuki\Handlers\Watchers;

use Nuki\Skeletons\Actors\Watcher;
use Nuki\Skeletons\Processes\Event;

/**
 * Created by PhpStorm.
 * User: herant
 * Date: 13-04-18
 * Time: 11:35
 */
class LogBeforeTerminate extends Watcher
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
        //Grab application form params
        ///** @var Application $app */
        //$app = $event->getParams()['app'];

        //Do logging logic
    }
}
