<?php

namespace CoiSA\Factory;

/**
 * Class ReflectionFactory
 *
 * @package CoiSA\Factory
 */
final class ReflectionFactory extends AbstractSharedFactory
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * ReflectionFactory constructor.
     *
     * @param string $className
     *
     * @throws \ReflectionException
     */
    public function __construct($className)
    {
        $this->reflectionClass = new \ReflectionClass($className);
    }

    /**
     * @param array|null $arguments
     *
     * @return object
     */
    public function newInstance(array $arguments = null)
    {
        if (null === $this->reflectionClass->getConstructor()) {
            return $this->reflectionClass->newInstanceWithoutConstructor();
        }

        if (empty($arguments)) {
            return $this->reflectionClass->newInstance();
        }

        return $this->reflectionClass->newInstanceArgs($arguments);
    }
}
