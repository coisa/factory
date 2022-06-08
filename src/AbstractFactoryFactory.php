<?php

declare(strict_types=1);

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020-2022 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\ReflectionException;

/**
 * Class AbstractFactoryFactory.
 *
 * @package CoiSA\Factory
 */
final class AbstractFactoryFactory implements FactoryInterface
{
    private string $abstractFactory;

    /**
     * AbstractFactoryFactory constructor.
     *
     * @throws ReflectionException
     */
    public function __construct(string $abstractFactory)
    {
        if (false === class_exists($abstractFactory)) {
            throw ReflectionException::forClassNotFound($abstractFactory);
        }

        $implements = class_implements($abstractFactory);

        if (false === \in_array(AbstractFactoryInterface::class, $implements, true)) {
            throw ReflectionException::forClassNotSubclassOf($abstractFactory, AbstractFactoryInterface::class);
        }

        $this->abstractFactory = $abstractFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $callable  = [$this->abstractFactory, 'create'];
        $arguments = \func_get_args();

        return \call_user_func_array($callable, $arguments);
    }
}
