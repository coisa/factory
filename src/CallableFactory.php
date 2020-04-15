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
 * Class CallableFactory
 *
 * @package CoiSA\Factory
 */
final class CallableFactory extends AbstractSharedFactory
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * CallableFactory constructor.
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * {@inheritDoc}
     */
    public function newInstance(array $arguments = null)
    {
        if (empty($arguments)) {
            return \call_user_func($this->callable);
        }

        return \call_user_func_array($this->callable, $arguments);
    }
}
