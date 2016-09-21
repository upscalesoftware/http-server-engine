<?php
namespace Upscale\HttpServerEngine;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use FastRoute\Dispatcher;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;

class DiConfig extends ContainerConfig
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
     * @param Container $di
     * @return void
     */
    public function define(Container $di)
    {
        parent::define($di);

        // Define shared services
        $di->set('request', $di->lazy([ServerRequestFactory::class, 'fromGlobals'], $_SERVER + $_ENV));

        // Define interface preferences
        $di->types[Container::class] = $di;
        $di->types[ContainerInterface::class] = $di;
        $di->types[ResponseInterface::class] = $di->lazyNew(Response::class);
        $di->types[ServerRequestInterface::class] = $di->lazyGet('request');
        $di->types[Dispatcher::class] = $di->lazy('\FastRoute\simpleDispatcher', $di->lazyNew(RouteCollector::class));

        // Define non-injectable arguments
        $di->params[RouteCollector::class]['routesConfigFilename'] = $this->routesConfigFilename;
        $di->params[FrontController::class]['routeErrorHandler'] = ErrorHandler\ResourceNotFound::class;
        $di->params[FrontController::class]['methodErrorHandler'] = ErrorHandler\MethodNotAllowed::class;
        $di->params[FrontController::class]['exceptionHandler'] = ErrorHandler\UncaughtException::class;
        $di->params[Server::class]['callback'] = $di->lazyNew(FrontController::class);
    }
}
