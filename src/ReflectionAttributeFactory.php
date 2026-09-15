<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use CoiSA\Factory\Attribute\FactoryAttribute;
use CoiSA\Factory\Exception\ReflectionException;
use CoiSA\Factory\Internal\FactoryDefinition;
use CoiSA\Factory\Registry\FactoryRegistryInterface;

final readonly class ReflectionAttributeFactory implements FactoryInterface
{
    private FactoryInterface $factory;

    public function __construct(object|string $objectOrClassName, ?FactoryRegistryInterface $registry = null)
    {
        try {
            $class = new \ReflectionClass($objectOrClassName);
        } catch (\ReflectionException $exception) {
            throw new ReflectionException($exception->getMessage(), 0, $exception);
        }

        $attributes = $class->getAttributes(FactoryAttribute::class);

        if ($attributes === []) {
            throw new ReflectionException(sprintf('Class "%s" has no FactoryAttribute.', $class->getName()));
        }

        // A selected attribute or factory may fail. That error must not trigger reflection fallback.
        $attribute = $attributes[0]->newInstance();
        $this->factory = $registry !== null && $registry->has($attribute->factory)
            ? $registry->get($attribute->factory)
            : FactoryDefinition::instantiate($attribute->factory);
    }

    public function create(mixed ...$arguments): mixed
    {
        return $this->factory->create(...$arguments);
    }
}
