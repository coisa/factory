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

use CoiSA\Factory\Annotation\FactoryAnnotation;
use PHPUnit\Framework\TestCase;

/**
 * Class DoctrineAnnotationFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @FactoryAnnotation(factory="CoiSA\Factory\Stub\Factory\TestFactory")
 *
 * @internal
 * @coversNothing
 */
final class DoctrineAnnotationFactoryTest extends TestCase
{
    public function testCreateWillReturnCreateFromFactoryAnnotation(): void
    {
        $self    = static::class;
        $factory = new DoctrineAnnotationFactory($self);

        parent::assertInstanceOf('CoiSA\\Factory\\Stub\\Factory\\TestFactory', $factory->create());
    }
}
