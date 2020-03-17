<?php

namespace CoiSA\Factory;

/**
 * Class SharedFactory
 *
 * @package CoiSA\Factory
 */
final class SharedFactory implements SharedFactoryInterface
{
    /**
     * @var array
     */
    private static $instances = array();

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
            self::$instances[$hash] = empty($arguments) ? StaticFactory::getInstance($className) :
                StaticFactory::getInstance($className, $arguments);
        }

        return self::$instances[$hash];
    }
}
