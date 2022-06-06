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
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class FactoryAnnotation.
 *
 * @package CoiSA\Factory\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes(
 *     @Attribute("factory", type="string")
 * )
 */
final class FactoryAnnotation
{
    /**
     * @var string
     *
     * @Required
     */
    public $factory;

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return AbstractFactory::getFactory($this->factory);
    }
}
