<?php

declare(strict_types=1);

namespace CoiSA\Factory\Exception;

use Psr\Container\NotFoundExceptionInterface;

final class ContainerNotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
