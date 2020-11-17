<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 *
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

/**
 * @deprecated only for retro-compatibility purpose
 */
\class_alias(
    'CoiSA\\Factory\\Exception\\FactoryExceptionInterface',
    'CoiSA\\Factory\\Exception\\FactoryException'
);
\class_alias(
    'CoiSA\\Factory\\AbstractFactory',
    'CoiSA\\Factory\\StaticFactory'
);
\class_alias(
    'CoiSA\\Factory\\AbstractFactoryInterface',
    'CoiSA\\Factory\\StaticFactoryInterface'
);
\class_alias(
    'CoiSA\\Factory\\AbstractFactoryFactory',
    'CoiSA\\Factory\\StaticFactoryProxyFactory'
);
