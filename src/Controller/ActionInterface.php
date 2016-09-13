<?php
namespace Upscale\Solvent\Controller;

use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    /**
     * Execute response preparation action
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function execute(ResponseInterface $response);
}
