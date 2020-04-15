<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Factory;

/**
 * Class AbstractSharedFactory
 *
 * @package CoiSA\Factory
 */
abstract class AbstractSharedFactory implements FactoryInterface
{
    /**
     * @var object[]
     */
    protected static $instances = array();

    /**
     * {@inheritDoc}
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
