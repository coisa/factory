<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use CoiSA\Factory\Attribute\FactoryAttribute;
use CoiSA\Factory\Exception\CircularDependencyException;
use CoiSA\Factory\Exception\ReflectionException;
use CoiSA\Factory\Registry\FactoryRegistry;
use CoiSA\Factory\Registry\FactoryRegistryInterface;
use Psr\Container\ContainerInterface;

/** Instance-scoped resolver; it never caches produced objects or changes PHP autoloading. */
final class Factory implements ResolverInterface
{
    /** @var list<string> */
    private array $resolving = [];

    /** @var \WeakMap<\Fiber<mixed, mixed, mixed, mixed>, list<string>> */
    private \WeakMap $fiberResolutions;

    public function __construct(
        private readonly ?ContainerInterface $container = null,
        private readonly FactoryRegistryInterface $registry = new FactoryRegistry(),
    ) {
        $this->fiberResolutions = new \WeakMap();
    }

    public function set(string $id, FactoryInterface|string $factory): void
    {
        $this->registry->set($id, $factory);
    }

    /** Whether an explicit registration exists; this does not probe metadata or the container. */
    public function has(string $id): bool
    {
        return $this->registry->has($id);
    }

    public function remove(string $id): bool
    {
        return $this->registry->remove($id);
    }

    public function clear(): void
    {
        $this->registry->clear();
    }

    public function alias(string $id, string $target): void
    {
        $this->set($id, new AliasFactory($this, $target));
    }

    public function create(string $id, array $arguments = []): mixed
    {
        $fiber = \Fiber::getCurrent();
        $path = $fiber === null ? $this->resolving : ($this->fiberResolutions[$fiber] ?? []);

        if (in_array($id, $path, true)) {
            throw new CircularDependencyException(
                'Circular factory resolution: ' . implode(' -> ', [...$path, $id]),
            );
        }

        $this->setResolutionPath($fiber, [...$path, $id]);

        try {
            return $this->getFactory($id)->create(...$arguments);
        } finally {
            $this->setResolutionPath($fiber, $path);
        }
    }

    public function getFactory(string $id): FactoryInterface
    {
        if ($this->registry->has($id)) {
            return $this->registry->get($id);
        }

        if ($this->container !== null && $this->container->has($id)) {
            return new ContainerFactory($this->container, $id);
        }

        try {
            $class = new \ReflectionClass($id);
        } catch (\ReflectionException $exception) {
            throw new ReflectionException($exception->getMessage(), 0, $exception);
        }

        if ($class->getAttributes(FactoryAttribute::class) !== []) {
            return new ReflectionAttributeFactory($class->getName(), $this->registry);
        }

        if ($class->implementsInterface(AbstractFactoryInterface::class)) {
            return new AbstractFactoryFactory($class->getName());
        }

        return new ReflectionClassFactory($class->getName());
    }

    /**
     * @param \Fiber<mixed, mixed, mixed, mixed>|null $fiber
     * @param list<string> $path
     */
    private function setResolutionPath(?\Fiber $fiber, array $path): void
    {
        if ($fiber === null) {
            $this->resolving = $path;
        } elseif ($path === []) {
            unset($this->fiberResolutions[$fiber]);
        } else {
            $this->fiberResolutions[$fiber] = $path;
        }
    }
}
