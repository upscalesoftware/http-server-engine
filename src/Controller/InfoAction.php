<?php
namespace Upscale\HttpServerEngine\Controller;

use Psr\Http\Message\ResponseInterface;

class InfoAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(ResponseInterface $response)
    {
        $response->getBody()->write('Solvent platform is running!');
        return $response;
    }
}
