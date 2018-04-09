<?php

namespace Nuki\Providers\Core\Framework;

use Nuki\Application\Application;
use Nuki\Handlers\Core\Resolver;
use Pimple\Container;

class Base implements \Pimple\ServiceProviderInterface {
    public function register(\Pimple\Container $pimple) {
        $appDir = dirname(getcwd());
        $pimple['unit-extenders'] = function() use ($pimple, $appDir) {
            $extendersContent = file_get_contents($appDir . \Nuki\Application\Application::EXTENDERS_INFO_PATH);
            if ($extendersContent === false) {
                return false;
            }

            $extenders = json_decode($extendersContent, true);
            if (!isset($extenders['Extenders']) || (!is_array($extenders['Extenders']))) {
                return false;
            }

            $activeUnit = str_replace(
                '\\',
                    '',
                    strstr(Application::getContainer()->offsetGet('active-unit'), '\\')
            );

            if (!isset($extenders['Extenders'][$activeUnit])) {
                return false;
            }

            return $extenders['Extenders'][$activeUnit];
        };
    }
}
