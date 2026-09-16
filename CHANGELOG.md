# Changelog

Notable changes are documented here. The project follows Semantic Versioning.

## Unreleased — 2.0.0

### Added

- Instance-scoped Factory resolver and registries with explicit removal and clearing.
- Positional/named construction argument forwarding, dynamic aliases and cycle detection with isolated Fiber bookkeeping.
- Behavioral tests for strategy precedence, selected-strategy failures, lifetimes, PSR-11 errors and substitution boundaries.
- Tested installation and executable legacy-to-DI, strategy and Prophecy examples.
- API, strategy, testing, troubleshooting and migration guides with a static documentation site.
- Portable agent maintenance guide and a self-contained Factory construction skill with a runnable dependency-substitution example.
- Supported-PHP and dependency-resolution CI, static analysis, dependency auditing and a manually gated Pages workflow.

### Changed

- Minimum PHP version is 8.3; the supported CI matrix is PHP 8.3–8.5.
- Factory interfaces declare variadic arguments and mixed returns.
- Prophecy adapters use caller-owned verification lifecycles.
- Exceptions use native local subclasses and retain previous errors; container not-found errors retain the PSR interface.
- Configured factory failures are visible and PSR-11 lookups reject construction arguments.

### Removed

- Global static facades/registry, legacy Composer class aliases and Doctrine annotations.
- The coisa/exceptions runtime dependency and its inherited static exception factories.
- PHP 7.4 platform emulation, abandoned tooling and the hard-coded Lando workflow.

No 2.0 tag or release has been published by these changes. Earlier release history remains available in the repository's Git tags.
