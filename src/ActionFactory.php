<?php
namespace Upscale\HttpServerEngine;

use Aura\Di\Container;

class ActionFactory
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
     * @param string $class
     * @param array $args
     * @return ActionInterface
     * @throws \UnexpectedValueException
     */
    public function create($class, array $args = [])
    {
        $result = $this->di->newInstance($class, $args);
        if (!$result instanceof ActionInterface) {
            throw new \UnexpectedValueException(sprintf(
                'Class "%s" has to implement "%s".', get_class($result), ActionInterface::class
            ));
        }
        return $result;
    }
}
