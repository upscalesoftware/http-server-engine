<?php
namespace Upscale\HttpServerEngine\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Upscale\HttpServerEngine\ActionInterface;

class ResourceNotFound implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ResponseInterface $response)
    {
        return $response->withStatus(404);
    }
}
