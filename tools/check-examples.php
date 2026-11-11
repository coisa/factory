<?php

declare(strict_types=1);

require __DIR__ . '/examples.php';

$examples = verifiedExamples();
echo sprintf("Verified %d executable examples and their exact output.\n", count($examples));
