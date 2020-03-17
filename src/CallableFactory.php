<?php

namespace CoiSA\Factory;

/**
 * Class CallableFactory
 *
 * @package CoiSA\Factory
 */
final class CallableFactory extends AbstractSharedFactory
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * CallableFactory constructor.
     *
     * @param $className
     *
     * @throws \ReflectionException
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param array|null $arguments
     *
     * @return object
     */
    public function newInstance(array $arguments = null)
    {
        if (empty($arguments)) {
            return call_user_func($this->callable);
        }

        return call_user_func_array($this->callable, $arguments);
    }
}
