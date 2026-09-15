# Executable examples

Read the source, run it locally, then change one construction boundary in your own application. Every example uses synthetic in-process behavior. No service credentials or network calls are needed.

| Example | What it demonstrates | Command |
| --- | --- | --- |
| Quickstart | Reflection, named arguments and a fixed override | `php examples/quickstart.php` |
| Legacy to DI | A direct static constructor moved behind a callback, a fake, then direct dependency injection | `php examples/legacy-to-di.php` |
| Strategies | A registered injected factory selected by attribute and alias, plus PSR-11 lookup | `php examples/strategies.php` |
| Prophecy | A caller-owned Prophet with an explicitly verified prediction | `php examples/prophecy.php` |

The [source directory](https://github.com/coisa/factory/tree/master/examples) contains the runnable files. Use the matching 2.0 checkout while the change is unreleased. `examples/expected.json` holds each exact output fixture. `composer --no-plugins examples` runs every file in a separate PHP process and fails on output drift, stderr, or a nonzero exit.

The generated site appends each file's current source and freshly checked output below. The build SHA in its footer identifies the source used. These are captured build results, not a browser PHP runtime.
