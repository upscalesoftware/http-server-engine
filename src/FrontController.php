<?php
namespace Upscale\Solvent;

use Aura\Di\Container;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FrontController
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Container
     */
    protected $di;

    /**
     * Inject dependencies
     *
     * @param Dispatcher $dispatcher
     * @param Container $di
     */
    public function __construct(Dispatcher $dispatcher, Container $di)
    {
        $this->dispatcher = $dispatcher;
        $this->di = $di;
    }

    /**
     * Delegate request handling to the respective controller action and return its execution result
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $response = $response->withStatus(404);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $response = $response->withStatus(405)->withHeader('Allow', implode(', ', $allowedMethods));
                break;

            case Dispatcher::FOUND:
                $actionClass = $routeInfo[1];
                $vars = $routeInfo[2];
                $actionInstance = $this->di->newInstance($actionClass, $vars);
                if ($actionInstance instanceof Controller\ActionInterface) {
                    $response = $actionInstance->execute($response);
                }
                break;
        }
        return $response;
    }

    /**
     * Shorthand to pass instance directly in place of callable arguments, including lazy injection via DI
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->dispatch($request, $response);
    }
}
