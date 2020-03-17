<?php

namespace CoiSA\Factory;

/**
 * Interface StaticFactoryInterface
 *
 * @package CoiSA\Factory
 */
interface StaticFactoryInterface
{
    /**
     * @param string $className
     * @param array $arguments
     *
     * @return object
     */
    public static function newInstance($className, array $arguments = null);

    /**
     * @param string $className
     * @param array $arguments
     *
     * @return object
     */
    public static function getInstance($className, array $arguments = null);
}
