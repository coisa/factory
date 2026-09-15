# Contributing

Use PHP 8.3+ and Composer 2. Run commands from this repository's root. Runtime dependencies are limited to PSR-11; test, analysis and site-build tools stay in `require-dev`.

Git must be available for the documentation build: it records the checked-out source SHA and whether the working tree has changes. Run documentation commands from a Git checkout, not a package archive.

```sh
composer install --no-plugins
composer --no-plugins check
composer --no-plugins audit
```

`check` validates Composer metadata, PHP syntax, PSR-12, PHPStan, PHPUnit, executable examples and the static documentation site. Warnings, deprecations, risky, skipped and incomplete tests fail the PHPUnit job. No external application data or live service is needed. A coverage driver is optional for local testing:

```sh
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text
```

The library intentionally does not commit `composer.lock`: CI resolves current allowed dependencies on PHP 8.3, 8.4 and 8.5, plus a lowest-dependency job on PHP 8.3. Applications should commit their own locks. When validating a dependency change, record the actual runtime and resolved versions; a platform override is not a runtime test.

## Construction contract

The [strategy guide](docs/strategies.md), [API](docs/api.md) and [migration guide](docs/migration.md) define the public 2.0 behavior. Keep precedence, argument forwarding, lifetimes, selected-strategy failures and prediction ownership consistent across code, tests and docs. Do not introduce global state, broad exception-swallowing or universal mocking claims.

Keep regression tests behavioral. Source code is under `src/`; optional Prophecy adapters remain inert until used. `tests/` contains only synthetic fixtures. Preserve the original license and author credit.

## Static documentation

Markdown in `docs/` is the documentation source. `tools/build-docs.php` uses CommonMark to produce `build/site/` with shared assets from `docs/assets/`. It executes every example and includes the exact checked output and source. `tools/check-docs.php` verifies site-local files and fragments, accessible document landmarks and that the output contains no PHP files. Generated output is ignored.

```sh
composer --no-plugins docs
php -S 127.0.0.1:8080 -t build/site
```

Inspect desktop and mobile widths, keyboard navigation, code overflow and every page. The development server binds to loopback. GitHub Pages serves only the generated static files and does not execute PHP.

PRs build and upload a documentation preview artifact without Pages permissions. The separate Pages deployment workflow is manual, accepts only the repository's default branch, checks the source before building, and uses the `github-pages` environment. Configure that environment and enable Pages using GitHub Actions before an owner-authorized deployment. Merging code alone does not deploy the site.

## Change and release workflow

Make changes on a topic branch, update the relevant tests and docs, add an Unreleased changelog entry, and open a reviewable PR with exact checks and known limitations. The current modernization targets a future 2.0.0 major release; publishing tags, releases, package versions or the site is a separate owner decision.

For a release, first validate the final commit on the supported matrix, review the migration contract, reconcile the release notes and then follow the owner's publishing decision. Do not publish from an unreviewed branch. To back out an unreleased change, revert its commit on a topic branch and rerun the same checks.
