<?php
namespace Upscale\Solvent\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class InfoAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, array $vars = [])
    {
        $response->getBody()->write('Solvent platform is running!');
        return $response;
    }
}
