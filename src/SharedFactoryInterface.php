<?php

namespace CoiSA\Factory;

/**
 * Interface SharedFactoryInterface
 *
 * @package CoiSA\Factory
 */
interface SharedFactoryInterface
{
    /**
     * @param string $className
     * @param array $arguments
     *
     * @return object
     */
    public function getShared($className, array $arguments = null);
}
