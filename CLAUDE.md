# CLAUDE.md

Guidance for working in this repository. Match the existing conventions exactly — this codebase is
small, uniform, and highly opinionated, so new code should be indistinguishable from what's here.

## What this is

A thin, strongly-typed PHP 8.5+ client for JSON and XML APIs. It wraps GuzzleHttp's `Client`,
(de)serializes request/response bodies, and — its main value-add — normalizes Guzzle's
transport-specific exceptions into a single, framework-agnostic hierarchy so callers can
`catch (ExceptionInterface)` and stay decoupled from Guzzle. It is a generic base library, not tied
to any specific API; other libraries (e.g. `php-smartthings-api-lib`) build on top of it. The entry
point is the `ApiClient` facade (`src/ApiClient.php`), which wires the senders and transformers
through a Symfony `ContainerBuilder` DI container.

## Commands

Binaries install into `bin/` (Composer `bin-dir`), not `vendor/bin/`. Both `bin/` and `vendor/` are
gitignored and Composer-installed, so run `composer install` first. The style tooling comes from the
private `christianjbrown/php-code-quality-scripts` dev dependency (php-cs-fixer + PHP_CodeSniffer,
**Symfony2 coding standard**); installing it needs SSH/`COMPOSER_AUTH` access to the private repo.

| Task | Command |
| --- | --- |
| Run tests + coverage (opens HTML report) | `composer test` |
| Run tests, no coverage | `php -d memory_limit=-1 ./bin/phpunit --no-coverage` |
| Run one test | `php -d memory_limit=-1 ./bin/phpunit --filter ApiRequestSenderTest` |
| Static analysis (PHPStan level max) | `composer stan` |
| Check code style | `composer check-style` |
| Auto-fix code style | `composer fix-style` |
| Check / fix style on git diff only | `composer check-style-diff` / `composer fix-style-diff` |

Always run `composer fix-style` first (php-cs-fixer auto-fixes what it can), then `composer
check-style` to surface remaining violations that must be fixed by hand, then `composer stan`, then
`composer test` before finishing. CI (`.github/workflows/ci.yml`) runs the same three gates —
style → PHPStan → PHPUnit-with-coverage — on push/PR to `main`.

## Architecture

Everything lives under the `ChristianBrown\ApiClient\` namespace (`src/`), mirrored 1:1 under
`ChristianBrown\ApiClient\Tests\` (`tests/`). A layered decorator design:

- **`ApiClient`** (`src/ApiClient.php`) — the facade/entry point. Constructed with no arguments, it
  builds a Symfony `ContainerBuilder` and registers Guzzle, the four transformers, and the three
  senders as services (ids are `SERVICE_*` constants on `ApiClientInterface`). Exposes
  `getApiRequestSender()`, `getJsonApiRequestSender()`, `getXmlApiRequestSender()`.
- **`ApiRequestSender`** — the raw HTTP layer over Guzzle's `ClientInterface`. `get`/`post`/`postForm`
  build a PSR-7 `Request`, send it, translate Guzzle exceptions into this library's own types, and
  return the raw response body as a `string`.
- **`JsonApiRequestSender` / `XmlApiRequestSender`** — decorate the raw sender, injecting transformers
  to (de)serialize. JSON senders return `array`; XML senders return `DOMDocument`.
- **`Transformer/`** — four single-responsibility (de)serializers, each behind an interface:
  `ArrayToJsonTransformer`, `JsonToArrayTransformer`, `StringToXmlDocTransformer`,
  `XmlDocToStringTransformer`. `transform()` takes the method/URL/query context only so a parse
  failure can report where it happened.
- **`Exception/`** — the normalized hierarchy, rooted at `ExceptionInterface extends Throwable`.
  Request branch: `ConnectException`. Response branch: `BadResponseException`,
  `TooManyRedirectsException` (carry the PSR-7 request + response, code = HTTP status). Parse branch:
  `ParseJsonException`, `ParseXmlException` (carry method/url/query context). A single
  `catch (ExceptionInterface)` covers everything this library throws.

## Conventions (follow all of these)

- `declare(strict_types=1);` on every file, immediately after `<?php`.
- **Every concrete class is `final` and implements a matching `...Interface`** in the same namespace
  (`ApiRequestSender`/`ApiRequestSenderInterface`, `ParseJsonException`/`ParseJsonExceptionInterface`).
- **Constants live on the interface, not the class**: container service ids
  (`ApiClientInterface::SERVICE_*`), HTTP method names (`ApiRequestSenderInterface::METHOD_*`), and
  all exception message templates (`*_SPRINTF`, `MESSAGE_*`). Message text never appears as a literal
  in a class body.
- **No constructor property promotion** — declare typed `private` properties and assign them in the
  constructor body. Class members (properties then methods) are ordered **alphabetically**.
- Import functions with `use function sprintf;` etc. (after class imports, blank line between), and
  call them unqualified.
- Full type declarations on all params/returns; express generics/array shapes via `@param`/`@return`/
  `@var` docblocks. Public methods that can throw carry `@throws` docblocks naming the concrete
  exception interface(s).
- Dependencies are constructor-injected and typed against interfaces (`ClientInterface`,
  `JsonToArrayTransformerInterface`, …) so everything is mockable.
- **`ApiClient` getters must stay PHPStan-safe**: `$this->container->get()` returns `mixed`, so assign
  it to a local `$service` annotated with a `/** @var XInterface $service */` docblock and return that
  — never `return $this->container->get(...)` directly.

### Deliberate deviation: abstract exception base classes

Unlike `php-smartthings-api-lib` (whose flat two-exception model has **no abstract base classes**),
this library **does** use abstract exception bases — `AbstractException`, `AbstractParseException`,
`AbstractResponseException`. They carry genuinely shared state and behavior (`getRequest()`,
`getResponse()`, setting the exception code to the HTTP status) across several concrete exceptions,
so they earn their place here. This is an intentional, isolated exception to the "no abstract base
classes" rule — do not remove them, and don't introduce new abstract bases elsewhere.

## Testing

The `phpunit.xml` config is strict (`requireCoverageMetadata`, `beStrictAboutCoverageMetadata`,
`failOnRisky`, `failOnWarning`, `restrictNotices`/`restrictWarnings`, path coverage). `<source>` sets
`ignoreIndirectDeprecations="true"` so Symfony DI's internal deprecations don't fail the suite.

- **Keep line, branch, method, class, AND path coverage at 100%.** Every branch — each Guzzle-exception
  translation and every parse-failure guard — must be exercised. Always run `composer test` and check
  the report (text summary to stdout + HTML at `.phpunit.cache/code-coverage-html/index.html`) before
  finishing. `phpunit.xml` sets `includeUncoveredFiles` and `pathCoverage`, so any untested
  file/line/branch/path shows up. The enforced gate is coverage *metadata* (`requireCoverageMetadata`)
  plus fail-on-risky/warning, but the whole suite currently sits at 100% on all five metrics — keep it
  there.
- **Path coverage and loops: prefer array functions over `foreach`.** xdebug counts a distinct "path"
  for every combination of branches through a method, and an explicit `foreach` — especially one with
  an internal `if` — generates back-edge permutation paths that no test input can reach, so a loop
  silently caps a method below 100% path coverage. Avoid explicit loops in favor of
  `array_filter`/`array_map`/`array_values` (function calls have no back-edge, so no phantom paths).
  This is exactly why `ParseXmlException` filters with `array_filter(...)` and builds messages with
  `array_map(...)` instead of `foreach`; match that idiom for any new list processing. Arrow functions
  (`static fn (...) => ...`) are fine as the callbacks here even though the rest of `src/` has no other
  closures. If you truly cannot avoid a loop, that method's path coverage will drop below 100% and you
  must call it out rather than adding contrived tests chasing the phantom paths.
- **Every test class needs a `#[CoversClass(...)]` attribute** (may list more than one) or the run
  fails. Use PHPUnit 12 **attributes, not annotations**: `#[CoversClass]`, `#[DataProvider]`,
  `#[TestWith]`.
- Tests mirror `src/` 1:1 under `tests/<Layer>/`, one `final class XTest extends TestCase` per class,
  methods named `test<Method><Scenario>`.
- **Double every collaborator, and pick the right kind of double** (PHPUnit 12 emits a notice for a
  `createMock()` that is never given an expectation, so don't reach for a mock by default):
  - **`self::createStub(SomeInterface::class)`** for a *pure return-value double* — one you only feed
    canned answers (`->method(...)->willReturn(...)`/`->willReturnCallback(...)`), throw as an
    exception, or pass through without configuring at all. Do **not** call `->with()` on a stub; it
    has no effect and is deprecated (removed in PHPUnit 13).
  - **`self::createMock(SomeInterface::class)` with `->expects(self::once())`** for a *verified
    collaborator* — one whose call you assert on via `->with(...)`. The `expects()` both enforces the
    interaction and satisfies the "must configure an expectation" check. The sender tests
    (`Json`/`Xml`) mock `apiRequestSender`/the transformers this way to prove the correct
    method/URL/query context is forwarded.
  - Both factories are **static**, so call them as `self::createStub(...)`/`self::createMock(...)`
    (matching the `self::assertSame(...)` assertion style), not `$this->...`.
- Assert statically (`self::assertSame`) and reference the **same interface constants** production
  code uses — for both data and expected exception messages — so no strings are hardcoded.
  `ApiRequestSenderTest` stubs Guzzle's `ClientInterface` (asserting on the outgoing PSR-7 request
  inside the send callback) and verifies each Guzzle exception is translated into the correct library
  exception with the original set as `getPrevious()`.

## Adding a feature

1. Add the class + its matching `...Interface` in the right layer, with any constants (service ids,
   message templates) on the interface.
2. If it's a new service, register it in `ApiClient::init()` using a new `SERVICE_*` constant.
3. Add a matching `#[CoversClass]` test under `tests/<Layer>/`, mocking all collaborators.
4. Run `composer fix-style`, then `composer check-style`, then `composer stan`, then `composer test`
   and **confirm the coverage report is 100%** on lines, paths, methods, and branches.
