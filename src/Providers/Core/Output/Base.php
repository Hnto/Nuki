<?php
namespace Nuki\Providers\Core\Output;

use Nuki\Handlers\Http\Output\{
    Renderers\FoilRenderer, Renderers\JsonRenderer, Renderers\RawRenderer, Response
};
use Nuki\Handlers\Provider\ProviderHandler;
use Nuki\Handlers\Repository\RepositoryHandler;

class Base implements \Pimple\ServiceProviderInterface {
    public function register(\Pimple\Container $pimple) {
        $pimple['provider-handler'] = function() use ($pimple) {
            $providerHandler = new ProviderHandler();
            
            foreach($pimple['providers'] as $key => $value) {
                $providerHandler->add($key, $value);
            }
            
            return $providerHandler;
        };
        
        $pimple['repository-handler'] = function() use ($pimple) {
            $repositoryHandler = new RepositoryHandler();
            
            foreach($pimple['repositories'] as $key => $value) {
                $repositoryHandler->add($key, $value);
            }
         
            return $repositoryHandler;
        };

        $appDir = dirname(getcwd());

        $pimple['response-handler'] = function() use ($appDir) {
            $renderingContent = file_get_contents($appDir . \Nuki\Application\Application::RENDERING_INFO_PATH);
            if ($renderingContent === false) {
                return false;
            }
            
            $renderingInfo = json_decode($renderingContent, true);
            $default = strtolower($renderingInfo['default']);

            $renderer = $this->buildRenderer($default, $renderingInfo);
            
            return new Response($renderer);
        };
        
        $pimple['providers'] = function() use ($appDir) {
            $providersContent = file_get_contents($appDir . \Nuki\Application\Application::PROVIDERS_INFO_PATH);
            if ($providersContent === false) {
                return false;
            }
            
            $providers = json_decode($providersContent, true);
            if (!isset($providers['Providers']) || (!is_array($providers['Providers']))) {
                return false;
            }
            
            return $providers['Providers'];
        };
        
        $pimple['repositories'] = function() use ($appDir) {
          $repositoriesContent = file_get_contents($appDir . \Nuki\Application\Application::REPOSITORIES_INFO_PATH);
          if ($repositoriesContent === false) {
              return false;
          }

          $repositories = json_decode($repositoriesContent, true);
          if (!isset($repositories['Repositories']) || (!is_array($repositories['Repositories']))) {
              return false;
          }

          return $repositories['Repositories'];
        };
    }
    
    /**
     * Build, setup and return renderer
     * 
     * @param string $render
     * @param array $renderingInfo
     * @return \Nuki\Skeletons\Handlers\Renderer
     */
    private function buildRenderer($render = 'foil', array $renderingInfo = []) {
      switch($render) {
        case "raw":
          $renderer = new RawRenderer();
          $renderInfo = [];
          break;
        
        case "json":
          $renderer = new JsonRenderer();
          $renderInfo = [];
          break;
        
        case "foil":
        default:
          $renderer = new FoilRenderer();
          $renderInfo['options'] = $renderingInfo['engines']['foil']['options'];
          $renderInfo['folders'] = $renderingInfo['engines']['foil']['folders'];
          break;
      }

      //Setup renderer
      $renderer->setup($renderInfo);
      
      return $renderer;
    }
}
