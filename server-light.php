<?php
/**
 * Entry point with lightweight bootstrap, but considerable amount of code in the entry point
 */
namespace Upscale\HttpServerEngine;

use Aura\Di\Container;
use Aura\Di\ContainerBuilder;
use function FastRoute\simpleDispatcher as createSimpleDispatcher;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;

require __DIR__ . '/autoload.php';

$request = ServerRequestFactory::fromGlobals($_SERVER + $_ENV);
$routeCollector = new RouteCollector(__DIR__ . '/config/routes.php');
$dispatcher = createSimpleDispatcher($routeCollector);

$diBuilder = new ContainerBuilder();
$di = $diBuilder->newInstance($diBuilder::AUTO_RESOLVE);
$di->types[Container::class] = $di;
$di->types[ContainerInterface::class] = $di;
$di->types[ServerRequestInterface::class] = $request;

$frontController = new FrontController($dispatcher, $di);

$server = new Server($frontController, $request, new Response());
$server->listen();
