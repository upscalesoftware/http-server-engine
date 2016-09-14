<?php
namespace Upscale\Solvent;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector as RouteCollection;

class RouteCollector
{
    /**
     * @var string
     */
    protected $routesConfigFilename;

    /**
     * Inject dependencies
     *
     * @param string $routesConfigFilename
     */
    public function __construct($routesConfigFilename)
    {
        $this->routesConfigFilename = $routesConfigFilename;
    }

    /**
     * Add routes to a given list
     *
     * @param RouteCollection $list
     * @return void
     */
    public function collect(RouteCollection $list)
    {
        $routes = require $this->routesConfigFilename;
        foreach ($routes as $routeInfo) {
            list($verb, $route, $handler) = $routeInfo;
            $list->addRoute($verb, $route, $handler);
        }
    }
}
