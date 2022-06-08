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
 * Class ReflectionException.
 *
 * @package CoiSA\Factory\Exception
 */
final class ReflectionException extends \CoiSA\Exception\Spl\ReflectionException implements FactoryExceptionInterface
{
    /** @const string */
    public const MESSAGE_ANNOTATION_CLASS_NOT_FOUND = 'Annotation "%s" not found on class definition.';

    public static function forAnnotationNotFound(string $annotation, int $code = 0, \Throwable $previous = null): self
    {
        $message = sprintf(
            self::MESSAGE_ANNOTATION_CLASS_NOT_FOUND,
            $annotation
        );

        return self::create($message, $code, $previous);
    }
}
