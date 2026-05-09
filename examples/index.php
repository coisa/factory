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

// $ docker run --rm -ti -v $(pwd):/app -w /app php:alpine php examples/index.php

use Symfony\Component\VarDumper\VarDumper;

$examples = [];

foreach (glob(__DIR__ . '/*.php') as $file) {
    if (__FILE__ === $file) {
        continue;
    }

    $examples += include $file;
}

VarDumper::dump($examples);
