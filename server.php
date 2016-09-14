<?php
namespace Upscale\Solvent;

use Aura\Di\ContainerBuilder;
use function FastRoute\simpleDispatcher as createSimpleDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;

require __DIR__ . '/autoload.php';

$request = ServerRequestFactory::fromGlobals($_SERVER + $_ENV);
$routeCollector = new RouteCollector(__DIR__ . '/config/routes.php');
$dispatcher = createSimpleDispatcher([$routeCollector, 'collect']);

$diBuilder = new ContainerBuilder();
$di = $diBuilder->newInstance($diBuilder::AUTO_RESOLVE);
$di->types[ServerRequestInterface::class] = $request;

$frontController = new FrontController($dispatcher, $di);

$server = new Server([$frontController, 'dispatch'], $request, new Response());
$server->listen();
