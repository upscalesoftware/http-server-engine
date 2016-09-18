<?php
namespace Upscale\HttpServerEngine;

use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    /**
     * Execute response preparation action
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(ResponseInterface $response);
}
