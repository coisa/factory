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

use Prophecy\Prophet;

/**
 * Class ProphesizeFactory.
 *
 * @package CoiSA\Factory
 */
final class ProphesizeFactory implements FactoryInterface
{
    private string $classOrInterface;

    /**
     * @var null|callable
     */
    private $prophesizeMethodsCallable;

    /**
     * ProphesizeFactory constructor.
     */
    public function __construct(string $classOrInterface, callable $prophesizeMethodsCallable = null)
    {
        $this->classOrInterface          = $classOrInterface;
        $this->prophesizeMethodsCallable = $prophesizeMethodsCallable;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $prophet        = new Prophet();
        $objectProphecy = $prophet->prophesize($this->classOrInterface);

        if ($this->prophesizeMethodsCallable) {
            \call_user_func($this->prophesizeMethodsCallable, $objectProphecy, \func_get_args());
        }

        return $objectProphecy->reveal();
    }
}
