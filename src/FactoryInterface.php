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
     * @param array $arguments
     *
     * @return object
     */
    public function newInstance(array $arguments = null);

    /**
     * @param array $arguments
     *
     * @return object
     */
    public function getInstance(array $arguments = null);
}
