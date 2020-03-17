<?php

namespace CoiSA\Factory;

/**
 * Class ReflectionFactory
 *
 * @package CoiSA\Factory
 */
final class ReflectionFactory implements FactoryInterface
{
    /**
     * @param string $className
     * @param array $arguments
     *
     * @return object
     * @throws \ReflectionException
     */
    public function newInstance($className, array $arguments = null)
    {
        $reflection = new \ReflectionClass($className);

        if (!$reflection->hasMethod('__construct')) {
            return $reflection->newInstanceWithoutConstructor();
        }

        if (empty($arguments)) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
