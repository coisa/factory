<?php

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
