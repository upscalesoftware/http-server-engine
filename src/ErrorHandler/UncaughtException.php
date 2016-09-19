<?php
namespace Upscale\HttpServerEngine\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Upscale\HttpServerEngine\HandlerInterface;

class UncaughtException implements HandlerInterface
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * Inject dependencies
     *
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ResponseInterface $response)
    {
        $response->getBody()->write($this->exception->getMessage());
        return $response->withStatus(500);
    }
}
