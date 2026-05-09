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

namespace CoiSA\Factory\Registry;

use PHPUnit\Framework\TestCase;

/**
 * Class FactoryRegistryTest.
 *
 * @package CoiSA\Factory\Registry
 *
 * @internal
 * @coversNothing
 */
final class FactoryRegistryTest extends TestCase
{
    public function testGetWithInvalidClassWillThrowOutOfBoundsException(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        FactoryRegistry::get(uniqid('test', true));
    }
}
