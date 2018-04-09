<?php

namespace Nuki\Handlers\Http;

use Nuki\Exceptions\Base;
use Nuki\Handlers\Core\Assist;
use Nuki\Handlers\Http\Input\Request;
use Zend\Stdlib\Parameters;

class Router
{

    /**
     * Contains the routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * Contains the last route
     * that was executed
     *
     * @var string
     */
    private $lastRouteUsed;

    /**
     * @var Request
     */
    private $request;

    /**
     * Contains the route execution
     *
     * @var string
     */
    private $routeExecution;

    const ROUTE_EXECUTION_CALLABLE = 'callable';
    const ROUTE_EXECUTION_SERVICE = 'service';

    const HTTP_METHOD_GET = 'get';
    const HTTP_METHOD_POST = 'post';
    const HTTP_METHOD_DELETE = 'delete';
    const HTTP_METHOD_PUT = 'put';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $uri
     * @param array|callable $route
     */
    public function get(string $uri, $route)
    {
        $params = $this->matchParams($uri);

        $this->add(
            $uri,
            self::HTTP_METHOD_GET,
            (is_callable($route) ? $route : new Parameters($route)),
            $params
        );
    }

    /**
     * @param string $uri
     * @param array|callable $route
     */
    public function post(string $uri, $route)
    {
        $params = $this->matchParams($uri);

        $this->add(
            $uri,
            self::HTTP_METHOD_POST,
            (is_callable($route) ? $route : new Parameters($route)),
            $params
        );
    }

    /**
     * @param string $uri
     * @param array|callable $route
     */
    public function delete(string $uri, $route)
    {
        $params = $this->matchParams($uri);

        $this->add(
            $uri,
            self::HTTP_METHOD_DELETE,
            (is_callable($route) ? $route : new Parameters($route)),
            $params
        );
    }

    /**
     * @param string $uri
     * @param array|callable $route
     */
    public function put(string $uri, $route)
    {
        $params = $this->matchParams($uri);

        $this->add(
            $uri,
            self::HTTP_METHOD_PUT,
            (is_callable($route) ? $route : new Parameters($route)),
            $params
        );
    }

    /**
     * @return array
     */
    public function routes()
    {
        return $this->routes;
    }

    /**
     * @param $route
     * @return bool
     */
    public function has($route)
    {
        foreach($this->routes as $routekey => $routeInfo) {

            $pattern = "@^" . preg_replace('/{[a-zA-Z0-9\_\-]+}/', '([a-zA-Z0-9\-\_]+)', $routekey) . "$@D";

            $matches = [];

            // check if the current request matches the expression
            if(preg_match($pattern, $route, $matches)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param $route
     * @return array|null
     */
    public function find($route)
    {
        foreach($this->routes as $routekey => $routeInfo) {

            $pattern = "@^" . preg_replace('/{[a-zA-Z0-9\_\-]+}/', '([a-zA-Z0-9\-\_]+)', $routekey) . "$@D";

            $matches = [];

            // check if the current request matches the expression
            if(preg_match($pattern, $route, $matches)) {

                $method = $routeInfo[strtolower($this->request->method())];
                $callable = isset($method['action']) ? $method['action'] : false;

                $this->setRouteExecution(
                    true === is_callable($callable) ?
                    self::ROUTE_EXECUTION_CALLABLE :
                    self::ROUTE_EXECUTION_SERVICE
                );

                return $routeInfo;
            }
        }

        return null;
    }

    /**
     * @param $uri
     * @return Parameters
     */
    protected function matchParams($uri)
    {
        $pattern = "@^" . preg_replace('/{[a-zA-Z0-9\_\-]+}/', '([a-zA-Z0-9\-\_]+)', $uri) . "$@D";

        $matches = [];

        $params = new Parameters($matches);

        // check if the current request matches the expression
        if(preg_match_all($pattern, Assist::extractBeforeNeedle($this->request->queryPath(), '?'), $matches)) {

            preg_match_all("/{([a-zA-Z0-9\_\-]+)}/m", $uri, $keys);

            //Shift off first element
            array_shift($matches);

            $params->exchangeArray(
                array_combine(
                    $keys[1],
                    Assist::extractSingleFromMultiArray(
                        $matches, 0
                    )
                )
            );
        }

        return $params;
    }

    /**
     * @param string $uri
     * @param $httpMethod
     * @param mixed $route
     * @param Parameters $params
     */
    protected function add(string $uri, string $httpMethod, $route, Parameters $params)
    {
        $this->routes[$uri][$httpMethod] = [
            'params' => $params->toArray(),
            'method' => $httpMethod
        ];

        if (is_callable($route)) {
            $this->routes[$uri][$httpMethod] = array_merge(
                $this->routes[$uri][$httpMethod],
                [
                    'action' => $route
                ]
            );
        }

        if ($route instanceof Parameters) {
            $this->routes[$uri][$httpMethod] = array_merge(
                $this->routes[$uri][$httpMethod],
                [
                    'unit' => $route->get(0, false),
                    'service' => $route->get(1, false),
                    'process' => $route->get(2, false),
                ]
            );
        }
    }

    /**
     * @param string $route
     */
    public function setLastRouteUsed($route)
    {
        $this->lastRouteUsed = $route;
    }

    /**
     * @return string
     */
    public function getLastRouteUsed()
    {
        return $this->lastRouteUsed;
    }

    /**
     * @param string $routeExecution
     */
    public function setRouteExecution($routeExecution)
    {
        $this->routeExecution = $routeExecution;
    }

    /**
     * @return string
     */
    public function getRouteExecution()
    {
        return $this->routeExecution;
    }

    /**
     * @return bool
     */
    public function routeIsCallable()
    {
        return $this->routeExecution === self::ROUTE_EXECUTION_CALLABLE;
    }

    /**
     * @return bool
     */
    public function routeIsServiced()
    {
        return $this->routeExecution === self::ROUTE_EXECUTION_SERVICE;
    }

    /**
     * @param Request $request
     *
     * @return Parameters
     *
     * @throws Base
     */
    public function findHttpRoute(Request $request)
    {
        $route = Assist::extractBeforeNeedle($request->queryPath(), '?');

        if (!$this->has($route)) {
            throw new Base('No route has been found for this url');
        }

        $data = $this->find($route);
        if (!in_array(strtolower(strtolower($request->method())), array_keys($data))) {
            throw new Base(
                'This route only accepts the "' .
                implode(', ', array_keys($data)) . '" http method(s)'
            );
        }

        $httpMethod = strtolower($request->method());

        return new Parameters($data[$httpMethod]);
    }
}
