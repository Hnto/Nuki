<?php
namespace Nuki\Providers\Core\Storage;

use Nuki\Application\Application;
use Nuki\Drivers\PDO;
use Nuki\Providers\StorageConnector;

class Base implements \Pimple\ServiceProviderInterface {
    public function register(\Pimple\Container $pimple) {
        $pimple['storage-handler'] = function() use ($pimple) {
            $appDir = dirname(getcwd());

            $connectionInfo = file_get_contents($appDir . Application::STORAGE_CONNECTION_INFO_PATH);

            if ($connectionInfo === false) {
                return false;
            }
            
            $connection = json_decode($connectionInfo, true);
            if (!isset($connection['connection-info']) || (!is_array($connection['connection-info']))) {
                return false;
            }

            $driverOptions = $connection['connection-info'];

            $driver = $pimple->offsetGet('storage-driver');
            switch($driver) {
                case "PDO":
                    $connector = new StorageConnector(new PDO($driverOptions));

                    return new \Nuki\Handlers\Database\PDO($connector);
                default:
                    return false;
            }
        };
    }
}
