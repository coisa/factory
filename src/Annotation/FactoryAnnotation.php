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

namespace CoiSA\Factory\Annotation;

use CoiSA\Factory\AbstractFactory;
use CoiSA\Factory\FactoryInterface;

/**
 * Class FactoryAnnotation.
 *
 * @package CoiSA\Factory\Annotation
 *
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
final class FactoryAnnotation
{
    private string $factory;

    public function __construct(string $factory)
    {
        $this->factory = $factory;
    }

    public function getFactory(): FactoryInterface
    {
        $implements = class_implements($this->factory);

        if (\in_array(FactoryInterface::class, $implements, true)) {
            return AbstractFactory::create($this->factory);
        }

        return AbstractFactory::getFactory($this->factory);
    }
}
