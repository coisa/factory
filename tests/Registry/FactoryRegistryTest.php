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

namespace CoiSA\Factory\Registry;

use PHPUnit\Framework\TestCase;

/**
 * Class FactoryRegistryTest
 *
 * @package CoiSA\Factory\Registry
 */
final class FactoryRegistryTest extends TestCase
{
    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetWithInvalidClassWillThrowOutOfBoundsException()
    {
        FactoryRegistry::get(\uniqid('test', true));
    }
}
