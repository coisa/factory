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
 * Class ProphesizeFactory
 *
 * @package CoiSA\Factory
 */
final class ProphesizeFactory extends ProphecyFactory
{
    /**
     * ProphesizeFactory constructor.
     *
     * @param null          $classOrInterface
     * @param null|callable $prophesizeMethodsCallable
     */
    public function __construct($classOrInterface = null, callable $prophesizeMethodsCallable = null)
    {
        $prophet        = StaticFactory::getInstance('Prophecy\Prophet');
        $objectProphecy = $prophet->prophesize($classOrInterface);

        parent::__construct($objectProphecy, $prophesizeMethodsCallable);
    }
}
