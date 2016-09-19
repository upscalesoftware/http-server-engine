<?php
namespace Upscale\HttpServerEngine;

use Aura\Di\Container;

class HandlerFactory
{
    /**
     * @var Container
     */
    protected $di;

    /**
     * Inject dependencies
     *
     * @param Container $di
     */
    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * Return newly created handler instantiated with given constructor arguments
     *
     * @param string $class
     * @param array $args
     * @return HandlerInterface
     * @throws \UnexpectedValueException
     */
    public function create($class, array $args = [])
    {
        $result = $this->di->newInstance($class, $args);
        if (!$result instanceof HandlerInterface) {
            throw new \UnexpectedValueException(sprintf(
                'Class "%s" has to implement "%s".', get_class($result), HandlerInterface::class
            ));
        }
        return $result;
    }
}
