<?php
namespace Upscale\Solvent;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher as createSimpleDispatcher;
use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Diactoros\ServerRequestFactory;

require __DIR__ . '/autoload.php';

$request = ServerRequestFactory::fromGlobals($_SERVER + $_ENV);

$dispatcher = createSimpleDispatcher(function (RouteCollector $r) {
    $routes = require __DIR__ . '/config/routes.php';
    foreach ($routes as $routeInfo) {
        list($verb, $route, $handler) = $routeInfo;
        $r->addRoute($verb, $route, $handler);
    }
});

$defaultResponse = new Response('php://memory', 400);

$frontController = new FrontController($dispatcher);

$server = new Server([$frontController, 'dispatch'], $request, $defaultResponse);
$server->listen();
