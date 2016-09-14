<?php
/**
 * Entry point with full-blown DI bootstrap and minimal amount of code in the entry point
 */
namespace Upscale\Solvent;

use Aura\Di\ContainerBuilder;
use Zend\Diactoros\Server;

require __DIR__ . '/autoload.php';

$diBuilder = new ContainerBuilder();
$di = $diBuilder->newConfiguredInstance([
    new DiConfig(__DIR__ . '/config/routes.php'),
], $diBuilder::AUTO_RESOLVE);

/** @var Server $server */
$server = $di->newInstance(Server::class);
$server->listen();
