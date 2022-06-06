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

namespace CoiSA\Factory\Exception;

/**
 * Class OutOfBoundsException.
 *
 * @package CoiSA\Factory\Exception
 */
final class OutOfBoundsException extends \CoiSA\Exception\Spl\OutOfBoundsException implements FactoryExceptionInterface
{
    /**
     * @param string     $class
     * @param mixed      $code
     * @param null|mixed $previous
     *
     * @return OutOfBoundsException
     */
    public static function forNotFoundClassFactory($class, $code = 0, $previous = null)
    {
        $message = sprintf(
            'Given class "%s" are not set into the Registry.',
            $class
        );

        return self::create($message, $code, $previous);
    }
}
