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

use CoiSA\Factory\Annotation\FactoryAnnotation;
use CoiSA\Factory\Attribute\FactoryAttribute;
use Doctrine\Common\Annotations\AnnotationRegistry;

class_alias(FactoryAnnotation::class, 'CoiSA\\Factory\\Annotation\\Factory');
class_alias(FactoryAttribute::class, 'CoiSA\\Factory\\Attribute\\Factory');

if (class_exists(AnnotationRegistry::class)) {
    AnnotationRegistry::registerUniqueLoader('class_exists');
}
