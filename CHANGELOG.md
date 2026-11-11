# Changelog

Notable changes are documented here. The project follows Semantic Versioning.

## Unreleased — 2.0.0

### Added

- Instance-scoped Factory resolver and registries with explicit removal and clearing.
- Opt-in `LegacyFactory` static entrypoint and late container installation, replacement or removal on a stable resolver; explicit in-place reset for sequential jobs and tests.
- Positional/named construction argument forwarding, dynamic aliases and cycle detection with isolated Fiber bookkeeping.
- Behavioral tests for strategy precedence, selected-strategy failures, lifetimes, PSR-11 errors and substitution boundaries.
- Tested installation and executable legacy bridge, static-to-DI, strategy and Prophecy examples, including PHP-DI autowiring without attributes and captured-versus-lazy factory lifetimes.
- API, strategy, testing, troubleshooting and migration guides with a static documentation site.
- Practical legacy adoption guide covering two-line bootstrap, preserved aliases and overrides, interface adapters, incremental injection and container cache ownership.
- Portable agent maintenance guide and an attributable `coisa-factory` skill with a runnable dependency-substitution example and legacy adoption procedure.
- Supported-PHP and dependency-resolution CI, static analysis, dependency auditing and a manually gated Pages workflow.

### Changed

- Minimum PHP version is 8.3; the supported CI matrix is PHP 8.3–8.5.
- Factory interfaces declare variadic arguments and mixed returns.
- Prophecy adapters use caller-owned verification lifecycles.
- Exceptions use native local subclasses and retain previous errors; container not-found errors retain the PSR interface.
- Configured factory failures are visible and PSR-11 lookups reject construction arguments.
- PHP-DI is a development-only dependency for the tested integration; core runtime dependencies remain PHP and PSR-11.

### Removed

- The old `AbstractFactory`, `FactoryFactory` and `FactoryAbstractFactory` facades and static registry API; opt-in migration now uses `LegacyFactory` or an application-owned resolver.
- Legacy Composer class aliases and Doctrine annotations.
- The coisa/exceptions runtime dependency and its inherited static exception factories.
- PHP 7.4 platform emulation, abandoned tooling and the hard-coded Lando workflow.

No 2.0 tag or release has been published by these changes. Earlier release history remains available in the repository's Git tags.
