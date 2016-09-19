<?php
namespace Upscale\HttpServerEngine;

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
     * @var HandlerFactory
     */
    protected $handlerFactory;

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
     * @param HandlerFactory $handlerFactory
     * @param string $routeErrorHandler Class to handle not found resources
     * @param string $methodErrorHandler Class to handle not allowed methods
     * @param string $exceptionHandler Class to handle uncaught exceptions
     */
    public function __construct(
        Dispatcher $dispatcher,
        HandlerFactory $handlerFactory,
        $routeErrorHandler,
        $methodErrorHandler,
        $exceptionHandler
    ) {
        $this->dispatcher = $dispatcher;
        $this->handlerFactory = $handlerFactory;
        $this->routeErrorHandler = $routeErrorHandler;
        $this->methodErrorHandler = $methodErrorHandler;
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * Delegate request processing to the respective handler and return its execution result
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
            $handler = $this->handlerFactory->create($handlerClass, $handlerArgs);
            $response = $handler->execute($response);
        } catch (\Exception $e) {
            $handler = $this->handlerFactory->create($this->exceptionHandler, ['exception' => $e]);
            $response = $handler->execute($response);
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
