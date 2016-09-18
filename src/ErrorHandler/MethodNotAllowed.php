<?php
namespace Upscale\HttpServerEngine\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Upscale\HttpServerEngine\ActionInterface;

class MethodNotAllowed implements ActionInterface
{
    /**
     * @var array
     */
    protected $allowedMethods;

    /**
     * @param array $allowedMethods
     */
    public function __construct(array $allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ResponseInterface $response)
    {
        return $response
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->allowedMethods));
    }
}
