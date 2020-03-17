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
     * @var array
     */
    private static $instances = array();

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

    /**
     * @param string $className
     * @param array|null $arguments
     *
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function getInstance($className, array $arguments = null)
    {
        $hash = self::getHash($className, $arguments);

        if (!isset(self::$instances[$hash])) {
            self::$instances[$hash] = $this->newInstance($className, $arguments);
        }

        return self::$instances[$hash];
    }

    /**
     * @param string $className
     * @param array|null $arguments
     *
     * @return string
     */
    private static function getHash($className, array $arguments = null)
    {
        if (empty($arguments)) {
            return $className;
        }

        return $className. '::' . \json_encode($arguments);
    }
}
