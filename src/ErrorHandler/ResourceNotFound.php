<?php
namespace Upscale\HttpServerEngine\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Upscale\HttpServerEngine\HandlerInterface;

class ResourceNotFound implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ResponseInterface $response)
    {
        return $response->withStatus(404);
    }
}
