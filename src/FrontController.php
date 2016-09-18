<?php
namespace Upscale\HttpServerEngine;

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
     * @var string
     */
    protected $routeErrorHandler;

    /**
     * @var string
     */
    protected $methodErrorHandler;

    /**
     * @var string
     */
    protected $exceptionHandler;

    /**
     * Inject dependencies
     *
     * @param Dispatcher $dispatcher
     * @param Container $di
     * @param string $routeErrorHandler Class to handle not found resources
     * @param string $methodErrorHandler Class to handle not allowed methods
     * @param string $exceptionHandler Class to handle uncaught exceptions
     */
    public function __construct(
        Dispatcher $dispatcher,
        Container $di,
        $routeErrorHandler,
        $methodErrorHandler,
        $exceptionHandler
    ) {
        $this->dispatcher = $dispatcher;
        $this->di = $di;
        $this->routeErrorHandler = $routeErrorHandler;
        $this->methodErrorHandler = $methodErrorHandler;
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * Delegate request handling to the respective action and return its execution result
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
                $handlerClass = $this->routeErrorHandler;
                $handlerArgs = [];
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $handlerClass = $this->methodErrorHandler;
                $handlerArgs = ['allowedMethods' => $allowedMethods];
                break;

            case Dispatcher::FOUND:
            default:
                $handlerClass = $routeInfo[1];
                $handlerArgs = $routeInfo[2];
                break;
        }
        try {
            $handler = $this->createHandler($handlerClass, $handlerArgs);
            $response = $handler->execute($response);
        } catch (\Exception $e) {
            $handler = $this->createHandler($this->exceptionHandler, ['exception' => $e]);
            $response = $handler->execute($response);
        }
        return $response;
    }

    /**
     * Return newly created handler instance instantiated with given constructor arguments
     *
     * @param string $class
     * @param array $args
     * @return ActionInterface
     * @throws \UnexpectedValueException
     */
    protected function createHandler($class, array $args = [])
    {
        $result = $this->di->newInstance($class, $args);
        if (!$result instanceof ActionInterface) {
            throw new \UnexpectedValueException(sprintf(
                'Class "%s" has to implement "%s".', get_class($result), ActionInterface::class
            ));
        }
        return $result;
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
