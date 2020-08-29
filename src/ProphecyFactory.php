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

use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ProphecyFactory
 *
 * @package CoiSA\Factory
 */
final class ProphecyFactory implements FactoryInterface
{
    /**
     * @var ObjectProphecy
     */
    private $objectProphecy;

    /**
     * @var null|callable
     */
    private $prophesizeMethodsCallable;

    /**
     * ProphecyFactory constructor.
     *
     * @param ObjectProphecy $objectProphecy
     * @param null|callable  $prophesizeMethodsCallable
     */
    public function __construct(ObjectProphecy $objectProphecy, callable $prophesizeMethodsCallable = null)
    {
        $this->objectProphecy            = $objectProphecy;
        $this->prophesizeMethodsCallable = $prophesizeMethodsCallable;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        if ($this->prophesizeMethodsCallable) {
            \call_user_func($this->prophesizeMethodsCallable, $this->objectProphecy, \func_get_args());
        }

        return $this->objectProphecy->reveal();
    }
}
