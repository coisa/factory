<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use PHPUnit\Framework\TestCase;

final class ExamplesTest extends TestCase
{
    public function testEveryDocumentedExecutableExampleMatchesItsOutputFixture(): void
    {
        $examples = verifiedExamples();
        self::assertCount(5, $examples);
        self::assertStringContainsString('Unchanged direct call: local: report', $examples['legacy-to-di.php']);
        self::assertStringContainsString('Predictions checked.', $examples['prophecy.php']);
    }
}
