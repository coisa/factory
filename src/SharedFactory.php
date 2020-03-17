<?php

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\ReflectionFactoryException;

/**
 * Class SharedFactory
 *
 * @package CoiSA\Factory
 */
final class SharedFactory implements SharedFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * SharedFactory constructor.
     *
     * @param FactoryInterface|null $factory
     */
    public function __construct(FactoryInterface $factory = null)
    {
        $this->factory = StaticFactory::getInstance(__NAMESPACE__ . '\\ReflectionFactory');
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

    /**
     * @param string $className
     * @param array $arguments optional
     *
     * @return object
     */
    public function getShared($className, array $arguments = null)
    {
        $hash = self::getHash($className, $arguments);

        if (!isset(self::$instances[$hash])) {
            self::$instances[$hash] = empty($arguments) ? $this->factory->newInstance($className) :
                $this->factory->newInstance($className, $arguments);
        }

        return self::$instances[$hash];
    }
}
