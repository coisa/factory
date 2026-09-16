# CoiSA Factory agent guide

## Purpose

Maintain a PHP library that makes object construction an explicit, replaceable boundary. This checkout contains the unreleased 2.0 development API for PHP 8.3+; CI covers PHP 8.3, 8.4 and 8.5.

## Ownership

The repository's maintainers own the public API and release decisions. This contract covers the entire repository. The construction skill owns its usage procedure in `SKILL.md`; no additional directory contracts are needed for the current layout.

Source, behavioral tests and public documentation must agree. Investigate a disagreement before changing the contract. Preserve the MIT license and existing author credit. Tags, package releases and GitHub Pages deployment require an explicit maintainer request.

## Local Contracts

- Keep source, documentation, examples and skill instructions in English. Document public API or lifecycle changes in the same PR, including migration guidance when behavior changes.
- Keep resolver registries isolated by default. Preserve explicit registry sharing, strategy precedence, argument forwarding and visible selected-strategy failures.
- Keep runtime dependencies separate from optional testing adapters and development tools. The core must run without Prophecy; its caller or test framework owns prediction verification.
- Limit replacement claims to explicit construction seams. Direct constructors/static calls, PHP type compatibility and final/internal-class restrictions retain ordinary PHP behavior.
- Keep `Internal` implementation details out of public compatibility promises. Do not add global aliases, implicit autowiring or product caching without an explicit API decision.
- Keep examples service-free and tests synthetic. Generated documentation belongs in ignored `build/site/`; temporary checks belong in ignored `tmp/`.

## Work Guidance

Use [CONTRIBUTING.md](CONTRIBUTING.md) for dependency setup, development commands and the documentation workflow. Read the relevant strategy, API or migration guide before changing that behavior. Keep tests focused on observable behavior and update executable examples when their documented result changes.

Use the [factory-construction skill](.agents/skills/factory-construction/SKILL.md) when helping a consumer choose a construction strategy, introduce a seam or substitute a test dependency. It is a repository-local, portable entrypoint; publishing or installing it into another environment is a separate action.

Make reviewable changes on a topic branch, preserve unrelated work, add an Unreleased changelog entry and sign off commits. Keep runtime evidence tied to the tested commit and distinguish local checks, remote CI, review, release and deployment state.

## Verification

Run commands from the repository root with PHP 8.3+, Composer 2 and Git available. After installing development dependencies with `composer install --no-plugins`:

```sh
composer --no-plugins check
composer --no-plugins audit
```

`check` validates Composer metadata, syntax, PSR-12, PHPStan, PHPUnit, executable examples and the static site. Runtime/API changes need the relevant behavioral tests and the supported CI matrix. For a documentation-only change, execute any new example, check its references and run `composer --no-plugins docs`; report exactly what was validated. A skill's valid frontmatter alone does not prove API correctness.

The documentation build runs PHP locally, records the source SHA and checks site links; GitHub Pages serves only static output. Inspect rendered desktop/mobile behavior when changing presentation. Do not edit generated output as documentation source.

## Child DOX Index

There are no child `AGENTS.md` contracts. These entrypoints provide the focused reading route:

- [README.md](README.md): package purpose and installation entrypoint; distinguishes this development line from published 1.x.
- [CONTRIBUTING.md](CONTRIBUTING.md): development and static-site commands; release and deployment remain maintainer decisions.
- [Strategies and lifetimes](docs/strategies.md): precedence, registrations and adapter lifetimes; no automatic dependency injection.
- [API and errors](docs/api.md): public signatures and error boundaries; excludes the `Internal` namespace.
- [Testing and limits](docs/testing.md): substitution and prediction ownership; no universal static/final/internal mocking.
- [Migration from 1.x](docs/migration.md): removed APIs and their explicit 2.0 replacements.
- [Factory construction skill](.agents/skills/factory-construction/SKILL.md): consumer-facing construction guidance and a runnable example; keeps its procedure in one package entrypoint.
