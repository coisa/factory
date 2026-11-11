<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\FactoryInterface;
use CoiSA\Factory\LegacyFactory;
use CoiSA\Factory\ValueFactory;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class HealthCheck
{
    public function status(): string
    {
        return 'ready';
    }
}

final class HealthCheckFactory implements FactoryInterface
{
    public function create(mixed ...$arguments): HealthCheck
    {
        return new HealthCheck();
    }
}

final readonly class ReportSettings
{
    public function __construct(public string $prefix)
    {
    }
}

final readonly class Report
{
    public function __construct(private ReportSettings $settings, private string $title = 'daily')
    {
    }

    public function summary(): string
    {
        return $this->settings->prefix . ': ' . $this->title;
    }
}

final readonly class ReportFactory implements FactoryInterface
{
    public function __construct(private ReportSettings $settings)
    {
    }

    public function create(mixed ...$arguments): Report
    {
        return new Report($this->settings, ...$arguments);
    }
}

// PHP-DI owns autowiring and its service cache. Factory depends only on PSR-11.
function reportContainer(string $prefix): ContainerInterface
{
    $builder = new ContainerBuilder();
    $builder->useAutowiring(true);
    $builder->useAttributes(false);
    $builder->addDefinitions([
        ReportSettings::class => new ReportSettings($prefix),
        'mode' => 'container',
    ]);
    return $builder->build();
}

LegacyFactory::reset();
try {
    $resolver = LegacyFactory::resolver();
    $resolver->alias('report', Report::class);
    $resolver->set('mode', new ValueFactory('fake'));
    $resolver->set('health', HealthCheckFactory::class);
    echo 'Before container: ', LegacyFactory::create(HealthCheck::class)->status(), PHP_EOL;
    echo 'No-argument factory: ', LegacyFactory::create('health')->status(), PHP_EOL;

    LegacyFactory::setContainer(reportContainer('first'));
    $first = LegacyFactory::create('report');
    echo 'Autowire: ', $first->summary(), PHP_EOL;
    echo 'Override: ', LegacyFactory::create('mode'), PHP_EOL;
    $captured = LegacyFactory::getFactory(Report::class);

    // Resolving a factory class produces that factory object. Register it to build reports.
    $resolver->set('configured.report', $resolver->create(ReportFactory::class));
    $resolver->set('lazy.report', new CallableFactory(
        static function (string $title) use ($resolver): Report {
            $builder = $resolver->create(ReportFactory::class);
            return $builder->create(title: $title);
        },
    ));

    LegacyFactory::setContainer(reportContainer('second'));
    echo 'Next selection: ', $resolver->create('report')->summary(), PHP_EOL;
    echo 'Captured strategy: ', $captured->create()->summary(), PHP_EOL;
    echo 'Configured factory: ', $resolver->create('configured.report', ['title' => 'weekly'])->summary(), PHP_EOL;
    echo 'Lazy factory: ', $resolver->create('lazy.report', ['title' => 'weekly'])->summary(), PHP_EOL;
    echo 'Existing product: ', $first->summary(), PHP_EOL;

    LegacyFactory::reset();
    echo 'Same resolver after reset: ', $resolver === LegacyFactory::resolver() ? 'yes' : 'no', PHP_EOL;
    echo 'Registration after reset: ', $resolver->has('mode') ? 'present' : 'absent', PHP_EOL;

    foreach (['job A', 'job B'] as $job) {
        try {
            // A new context gets a new container; reset cannot purge a container's cache.
            LegacyFactory::setContainer(reportContainer($job));
            echo 'Job: ', LegacyFactory::create(Report::class)->summary(), PHP_EOL;
        } finally {
            LegacyFactory::reset();
        }
    }
} finally {
    LegacyFactory::reset();
}
