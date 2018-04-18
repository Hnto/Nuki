<?php

namespace Nuki\Providers\Core\Input;

use Nuki\Handlers\{
    Http\Input\Request, Http\Session, Process\EventHandler, Provider\Params
};

class Base implements \Pimple\ServiceProviderInterface {
    public function register(\Pimple\Container $pimple) {
        $pimple['request-handler'] = function() {
            return new Request();
        };

        $pimple['session-handler'] = function() {
            return new Session();
        };

        $pimple['event-handler'] = function() {
            return new EventHandler();
        };
        
        $pimple['params-handler'] = function() {
          return new Params();
        };
        
        //Create config handler
        $appDir = dirname(getcwd());
        $dir = new \DirectoryIterator($appDir . '/settings/Config');
        $configVars = [];
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isFile()) {
                continue;
            }

            $configVars[] = include $fileinfo->getPathname();
        }
        $pimple['config-handler'] = function() use ($configVars) {
            return new \Adbar\Dot($configVars);
        };
    }
}
