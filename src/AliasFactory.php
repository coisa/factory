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

/**
 * Class AliasFactory.
 *
 * @package CoiSA\Factory
 */
final class AliasFactory implements FactoryInterface
{
    private string $factoryAlias;

    /**
     * AliasFactory constructor.
     */
    public function __construct(string $factoryAlias)
    {
        $this->factoryAlias = $factoryAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $arguments = \func_get_args();
        $factory   = AbstractFactory::getFactory($this->factoryAlias);

        return \call_user_func_array([$factory, 'create'], $arguments);
    }
}
