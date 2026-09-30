# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Request timeouts. The Guzzle client used to be built with none, so a request to an API that stopped
  responding waited forever. It now gives up after 30 seconds, or 10 seconds without a connection.
  Pass `new ApiClient(new ApiClientContainerFactory(new ClientOptions($timeout, $connectTimeout)))` to
  change either, or 0 to wait indefinitely as before.
- `TransferException`, thrown when a request fails in transit for a reason other than failing to
  connect, such as a connection dropped mid-response or a rejected TLS certificate. Guzzle reports
  these as a plain `RequestException`, which used to escape unwrapped, bypassing both
  `ExceptionInterface` and the redaction.

### Changed

- The Guzzle exception on `getPrevious()` is now a copy built around the redacted request, not the
  original. The original held the request exactly as sent, so the `Authorization` header came back out
  through the exception chain.
- The request stored on exceptions also loses the `apikey`, `X-Api-Key` and `Cookie` headers, and its
  body is emptied, since a form body can carry a refresh token or client secret.
- `ApiRequestSender` takes a `GuzzleExceptionRedactorInterface` as a second constructor argument.
  `ApiClient` wires it for you. Code that builds `ApiRequestSender` itself has to pass one.
- `ApiClientContainerFactory` takes a `ClientOptionsInterface` as a constructor argument. `new ApiClient()`
  passes the defaults for you.

## [1.0.0] - 2026-09-28

First stable release.

### Added

- `ApiClient`, a facade that wires up a raw, a JSON and an XML request sender on top of Guzzle.
- `get`, `post`, `put`, `patch` and `delete` on the raw and JSON senders, plus `postForm`, `putForm`
  and `patchForm` for `application/x-www-form-urlencoded` bodies. The JSON sender decodes responses to
  an `array` and the XML sender to a `DOMDocument`.
- A single exception hierarchy rooted at `ExceptionInterface`, so callers can catch everything the
  library throws without depending on Guzzle: connection failures, bad responses (the exception code is
  the HTTP status), too many redirects, and JSON or XML parse failures.
- `getDecodedBody()` on response exceptions, which returns the JSON error payload as an array, or `null`
  when the body is not a JSON array or object.
- `application/json` as the default `Content-Type` on `JsonApiRequestSender::post()`, as `postForm()`
  does for form bodies. A `Content-Type` header you pass yourself wins.
- Redaction of the `Authorization` and `Proxy-Authorization` headers from the request stored on
  exceptions, so tokens do not end up in logs or error reports.
- `RequestContext`, a value object carrying the method, URL and query string, exposed on parse
  exceptions so a failure reports where it happened.

[Unreleased]: https://github.com/christianjbrown/api-client-php/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/christianjbrown/api-client-php/releases/tag/v1.0.0
