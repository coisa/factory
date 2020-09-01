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

namespace CoiSA\Factory\Exception;

/**
 * Class ReflectionException
 *
 * @package CoiSA\Factory\Exception
 */
final class ReflectionException extends \ReflectionException implements FactoryException
{
    /**
     * @param \ReflectionException $reflectionException
     *
     * @return ReflectionException
     */
    public static function fromReflectionException(\ReflectionException $reflectionException)
    {
        return new self(
            $reflectionException->getMessage(),
            $reflectionException->getCode(),
            $reflectionException
        );
    }
}
