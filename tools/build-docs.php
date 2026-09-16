<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/examples.php';

use League\CommonMark\GithubFlavoredMarkdownConverter;

$root = dirname(__DIR__);
$output = $root . '/build/site';
if (!is_dir($output) && !mkdir($output, 0777, true) && !is_dir($output)) {
    throw new RuntimeException('Cannot create site output directory.');
}

$pages = [
    'index' => ['Start here', 'docs/quickstart.md'],
    'legacy-adoption' => ['Legacy adoption', 'docs/legacy-adoption.md'],
    'strategies' => ['Strategies & lifetimes', 'docs/strategies.md'],
    'api' => ['API & errors', 'docs/api.md'],
    'examples' => ['Executable examples', 'docs/examples.md'],
    'testing' => ['Testing & limits', 'docs/testing.md'],
    'migration' => ['Migrate from 1.x', 'docs/migration.md'],
    'contributing' => ['Contributing', 'CONTRIBUTING.md'],
    'changelog' => ['Changelog', 'CHANGELOG.md'],
];
$examples = verifiedExamples();
$converter = new GithubFlavoredMarkdownConverter(['html_input' => 'strip', 'allow_unsafe_links' => false]);

function gitOutput(string ...$arguments): string
{
    $process = proc_open(['git', ...$arguments], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, dirname(__DIR__));
    if (!is_resource($process)) {
        throw new RuntimeException('Cannot read source identity.');
    }
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    if (proc_close($process) !== 0) {
        throw new RuntimeException('Cannot read source identity: ' . $stderr);
    }
    return trim($stdout);
}

$sha = gitOutput('rev-parse', 'HEAD');
$dirty = gitOutput('status', '--porcelain', '--untracked-files=normal') !== '';
$sourceIdentity = $sha . ($dirty ? ' (working tree changes)' : '');
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$linkMap = [];
foreach ($pages as $slug => [, $source]) {
    $linkMap[$source] = $slug . '.html';
    $linkMap[basename($source)] = $slug . '.html';
}
$linkMap['LICENSE'] = 'license.txt';

foreach ($pages as $slug => [$title, $source]) {
    $markdown = file_get_contents($root . '/' . $source);
    if ($slug === 'examples') {
        foreach ($examples as $file => $result) {
            $markdown .= "\n## " . $file . "\n\n### Source\n\n";
            $markdown .= "```php\n" . file_get_contents($root . '/examples/' . $file) . "```\n\n";
            $markdown .= "### Verified output\n\n```text\n" . $result . "```\n";
        }
    }
    $content = (string) $converter->convert($markdown);
    $rewriteLink = static function (array $match) use ($linkMap): string {
        return 'href="' . ($linkMap[$match[1]] ?? $match[1]) . ($match[2] ?? '') . '"';
    };
    $content = preg_replace_callback('/href="([^"#]+)(#[^"]*)?"/', $rewriteLink, $content);
    $headings = [];
    $addHeadingId = static function (array $match) use (&$headings): string {
        $text = html_entity_decode(strip_tags($match[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $id = trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $text)), '-');
        $count = ($headings[$id] ?? 0) + 1;
        $headings[$id] = $count;
        $id .= $count > 1 ? '-' . $count : '';
        return '<h' . $match[1] . ' id="' . $id . '">' . $match[2] . '</h' . $match[1] . '>';
    };
    $content = preg_replace_callback('/<h([1-6])>(.*?)<\/h\1>/s', $addHeadingId, $content);
    $navigation = '';
    foreach ($pages as $target => [$label]) {
        $current = $target === $slug ? ' aria-current="page"' : '';
        $navigation .= '<a href="' . $target . '.html"' . $current . '>' . $escape($label) . '</a>';
    }
    ob_start();
    require $root . '/docs/template.php';
    file_put_contents($output . '/' . $slug . '.html', ob_get_clean());
}
foreach (['style.css', 'site.js'] as $asset) {
    copy($root . '/docs/assets/' . $asset, $output . '/' . $asset);
}
copy($root . '/LICENSE', $output . '/license.txt');
file_put_contents($output . '/.nojekyll', '');
file_put_contents($output . '/build.json', json_encode([
    'source_sha' => $sha,
    'working_tree_changes' => $dirty,
    'php_version' => PHP_VERSION,
    'verified_examples' => array_keys($examples),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
echo sprintf(
    "Built %d static pages; %d examples verified; source %s.\n",
    count($pages),
    count($examples),
    $sourceIdentity,
);
