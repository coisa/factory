<?php

namespace CoiSA\Factory;

/**
 * Class AbstractSharedFactory
 *
 * @package CoiSA\Factory
 */
abstract class AbstractSharedFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected static $instances = array();

    /**
     * @param array|null $arguments
     *
     * @return object
     */
    public function getInstance(array $arguments = null)
    {
        # PHP 5.3 compatibility
        $className = \get_called_class();

        $hash = \serialize($arguments);

        if (!isset($className::$instances[$hash])) {
            $className::$instances[$hash] = $this->newInstance($arguments);
        }

        return $className::$instances[$hash];
    }
}
