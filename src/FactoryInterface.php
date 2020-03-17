<?php

namespace CoiSA\Factory;

/**
 * Interface FactoryInterface
 *
 * @package CoiSA\Factory
 */
interface FactoryInterface
{
    /**
     * @param string $className
     * @param array $arguments
     *
     * @return object
     */
    public function newInstance($className, array $arguments = null);

    /**
     * @param string $className
     * @param array $arguments
     *
     * @return object
     */
    public function getInstance($className, array $arguments = null);
}
