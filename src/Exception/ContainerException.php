<?php

declare(strict_types=1);

namespace CoiSA\Factory\Exception;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \RuntimeException implements FactoryExceptionInterface, ContainerExceptionInterface
{
}
