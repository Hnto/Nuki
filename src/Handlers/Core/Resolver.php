<?php

namespace Nuki\Handlers\Core;

use Nuki\Application\Application;
use Nuki\Exceptions\Base;

class Resolver {

    /**
     * @param $class
     * @param bool $method
     * @param Application $app
     * @return object
     * @throws Base
     */
    public function resolve($class, $method = false, Application $app = null)
    {
        $reflector = new \ReflectionClass($class);

        if(!$reflector->isInstantiable()) {
            throw new Base("[$class] is not instantiable");
        }

        if ($method !== false) {
            try {
                return $this->resolveMethod($reflector, $method, $app);
            } catch (\Exception $e) {
                throw new Base($e->getMessage());
            }
        }

        try {
            return $this->resolveConstructor($reflector, $class, $app);
        } catch (\Exception $e) {
            throw new Base($e->getMessage());
        }
    }

    /**
     * @param \ReflectionClass $reflector
     * @param $class
     * @param Application $app
     * @return object
     * @throws \Exception
     */
    private function resolveConstructor(\ReflectionClass $reflector, $class, Application $app = null)
    {
        $constructor = $reflector->getConstructor();

        if(is_null($constructor))
        {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters, $app);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param \ReflectionClass $reflector
     * @param string $method
     * @param Application $app
     * @return mixed
     * @throws \Exception
     */
    private function resolveMethod(\ReflectionClass $reflector, string $method, Application $app = null)
    {
        if (!$reflector->hasMethod($method)) {
            throw new Base('no method found');
        }

        $parameters = $reflector->getMethod($method)->getParameters();
        $dependencies = $this->getDependencies($parameters, $app);

        $methodReflector = new \ReflectionMethod($reflector->getName(), $method);

        return $methodReflector->invokeArgs(
            $reflector->newInstance($app),
            $dependencies
        );
    }

    /**
     * @param $parameters
     * @param Application $app
     * @return array
     * @throws \Exception
     */
    public function getDependencies($parameters, Application $app = null)
    {
        $dependencies = [];

        foreach($parameters as $parameter)  {
            $dependency = $parameter->getClass();

            if(null === $dependency) {
                $dependencies[] = $this->resolveNonClass($parameter);
                continue;
            }

            if ($dependency->getName() === Application::class) {
                $dependencies[] = $app;
                continue;
            }

            $dependencies[] = $this->resolve($dependency->name, false, $app);
        }
        return $dependencies;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return mixed
     * @throws Base
     */
    public function resolveNonClass(\ReflectionParameter $parameter)
    {
        if($parameter->isDefaultValueAvailable())
        {
            return $parameter->getDefaultValue();
        }

        throw new Base("Nothing to resolve");
    }
}
