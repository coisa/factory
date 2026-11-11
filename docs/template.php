<?php

declare(strict_types=1);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CoiSA Factory: explicit construction strategies for PHP, legacy-to-DI examples, tested boundaries and migration guidance.">
    <meta name="color-scheme" content="light">
    <title><?= $escape($title) ?> · CoiSA Factory</title>
    <link rel="stylesheet" href="style.css">
    <script src="site.js" defer></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="topbar">
    <a class="brand" href="index.html" aria-label="CoiSA Factory home">
        <span class="brand-mark" aria-hidden="true">F</span>
        <span>CoiSA <strong>Factory</strong></span>
    </a>
    <span class="version">2.0-dev</span>
    <a class="source-link" href="https://github.com/coisa/factory">Source ↗</a>
</header>
<?php if ($slug === 'index') : ?>
<section class="hero" aria-labelledby="hero-title">
    <div>
        <p class="eyebrow">A small construction toolkit · PHP 8.3+</p>
        <h1 id="hero-title">Build a seam.<br><em>Keep your options.</em></h1>
        <p class="hero-description">Make object creation a deliberate boundary. Keep legacy behavior,
            introduce a test double, then move toward dependency injection—one call at a time.</p>
        <div class="hero-actions">
            <a class="button" href="#installation-quickstart">Start building <span aria-hidden="true">↓</span></a>
            <a class="text-link" href="examples.html">Read the working examples ↗</a>
        </div>
    </div>
    <div class="construction" aria-label="Application request flows through a selected factory strategy to a produced value">
        <div class="diagram-label"><span class="status-dot"></span> Explicit by design</div>
        <div class="step"><span>01</span><strong>Your application</strong><small>request an object</small></div>
        <div class="connector" aria-hidden="true">↓</div>
        <div class="step selected"><span>02</span><strong>Factory</strong><small>choose the construction strategy</small></div>
        <div class="connector" aria-hidden="true">↓</div>
        <div class="step"><span>03</span><strong>A useful instance</strong><small>real, adapted, or interface-compatible fake</small></div>
        <p>Isolated by default. An opt-in legacy bridge.</p>
    </div>
</section>
<section class="principles" aria-label="Library principles">
    <div><span>01 / CONTROL</span><strong>Explicit construction</strong><p>Replace the call boundary you choose.</p></div>
    <div><span>02 / ISOLATION</span><strong>Your instance, your state</strong><p>Fresh registries by default. Shared state by choice.</p></div>
    <div><span>03 / FEEDBACK</span><strong>Failures stay visible</strong><p>Selected strategy errors never become silent fallback.</p></div>
</section>
<?php endif; ?>
<div class="reading-layout">
    <aside>
        <p class="nav-title">FIELD GUIDE</p>
        <nav aria-label="Documentation"><?= $navigation ?></nav>
        <div class="sidebar-note">Small enough to understand.<br>Explicit enough to replace.</div>
    </aside>
    <main id="main" tabindex="-1">
        <div class="article-label">FACTORY / <?= $escape(strtoupper($title)) ?></div>
        <article><?= $content ?></article>
<?php if ($slug === 'index') : ?>
        <section class="strategy-picker" aria-labelledby="picker-title">
            <p class="eyebrow">Choose your entry point</p>
            <h2 id="picker-title">Where is construction happening?</h2>
            <label for="seam-choice">The call you want to control</label>
            <select id="seam-choice">
                <option value="constructor">A normal class constructor</option>
                <option value="static">A legacy static factory method</option>
                <option value="container">An existing PSR-11 container</option>
                <option value="fake">An interface-compatible test fake</option>
            </select>
            <p id="seam-advice" aria-live="polite">Start with ReflectionClassFactory or Factory::create().
                Register an override at that explicit call boundary when needed.</p>
            <a href="strategies.html">Compare all strategies →</a>
        </section>
<?php endif; ?>
    </main>
</div>
<footer>
    <div><strong>CoiSA Factory</strong><span>By Felipe Sayão Lobato Abreu · <a href="license.txt">MIT</a></span></div>
    <p>Static reference. PHP examples run at build time.<br>
        <a href="build.json">Source <?= $escape(substr($sha, 0, 12)) ?></a><?= $dirty ? ' · working tree changes' : '' ?></p>
</footer>
</body>
</html>
