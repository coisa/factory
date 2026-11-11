<?php

declare(strict_types=1);

namespace CoiSA\Factory\Exception;

final class CircularDependencyException extends \LogicException implements FactoryExceptionInterface
{
}
