<?php
namespace Upscale\Solvent\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ActionInterface
{
    /**
     * Execute response preparation action
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $vars Route variables
     * @return ResponseInterface
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, array $vars = []);
}
