<?php

namespace Nuki\Providers\Core\Helpers;

use Nuki\Handlers\Core\Resolver;
use Pimple\Container;

class Base implements \Pimple\ServiceProviderInterface {
    public function register(\Pimple\Container $pimple) {
        $pimple['helpers'] = function() use ($pimple) {
          $helpers = [];

          $helpers['\Nuki\Handlers\Process\Authentication'] = $this->authentication($pimple);
          $helpers['\Nuki\Handlers\Process\Authorization'] = $this->authorization($pimple);
          $helpers['\Nuki\Handlers\Core\Resolver'] = new Resolver();

          return $helpers;
        };
    }
    
    /**
     * Return authentication helper info
     * 
     * @param Container $pimple
     * @return array
     */
    private function authentication(Container $pimple) {
      return [
        'request' => $pimple['request-handler'],
        'ids' => [
          'username' => [
            'username', 'x-username',
            'x-id', 'user',
            'pincode', 'x-user'
          ],
          'password' => [
            'secret', 'x-pass', 'x-password'
          ],
        ],
      ];
    }
    
    /**
     * Return authorization helper info
     * 
     * @param Container $pimple
     * @return array
     */
    private function authorization(Container $pimple) {
      return [
        'request' => $pimple['request-handler'],
        'ids' => [
          'token' => [
            'token', 'x-token'
          ],
        ],
      ];
    }
}
