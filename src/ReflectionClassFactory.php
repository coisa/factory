<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\ReflectionException;

final readonly class ReflectionClassFactory implements FactoryInterface
{
    /** @var \ReflectionClass<object> */
    private \ReflectionClass $class;

    public function __construct(object|string $objectOrClassName)
    {
        try {
            $this->class = new \ReflectionClass($objectOrClassName);
        } catch (\ReflectionException $exception) {
            throw new ReflectionException($exception->getMessage(), 0, $exception);
        }

        if (!$this->class->isInstantiable()) {
            throw new ReflectionException(sprintf('Class "%s" is not instantiable.', $this->class->getName()));
        }
    }

    public function create(mixed ...$arguments): object
    {
        return $this->class->newInstanceArgs($arguments);
    }
}
