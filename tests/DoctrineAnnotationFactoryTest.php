<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 *
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */
namespace CoiSA\Factory;

use CoiSA\Factory\Annotation\FactoryAnnotation;
use PHPUnit\Framework\TestCase;

/**
 * Class DoctrineAnnotationFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @FactoryAnnotation(factory="CoiSA\Factory\Stub\Factory\TestFactory")
 */
final class DoctrineAnnotationFactoryTest extends TestCase
{
    public function testCreateWillReturnCreateFromFactoryAnnotation()
    {
        $self    = \get_called_class();
        $factory = new DoctrineAnnotationFactory($self);

        self::assertInstanceOf('CoiSA\\Factory\\Stub\\Factory\\TestFactory', $factory->create());
    }
}
