<?php
namespace Upscale\HttpServerEngine;

use Psr\Http\Message\ResponseInterface;

interface HandlerInterface
{
    /**
     * Perform response preparation and return its revised instance
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(ResponseInterface $response);
}
