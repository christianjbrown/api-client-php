# API Client

[![CI](https://github.com/christianjbrown/php-api-client-lib/actions/workflows/ci.yml/badge.svg)](https://github.com/christianjbrown/php-api-client-lib/actions/workflows/ci.yml)

This library provides a simple request client for JSON and XML APIs. It is a wrapper around GuzzleHttp's `Client` class that decodes responses — JSON to an `array`, XML to a `DOMDocument` — and provides common non-Guzzle specific exception classes to make it easier to catch and handle.



## :heavy_check_mark: Prerequisites

- [Git](https://git-scm.com/)
- [PHP](https://www.php.net/) 8.3 or higher (8.x)
- [Composer](https://getcomposer.org/)

:bulb: If you're on MacOS and have [Homebrew](https://brew.sh/), PHP and Composer will install with `brew install composer`.



## :building_construction: Installation

For your composer-enabled project:

```bash
composer require christianjbrown/php-api-client-lib
```



## :computer: Usage



### Setup



```php
use ChristianBrown\ApiClient\ApiClient;

$apiClient = new ApiClient();
$jsonApiRequestSender = $apiClient->getJsonApiRequestSender();

// or, for XML use
// $xmlApiRequestSender = $apiClient->getXmlApiRequestSender();
```



### `POST` examples



If you need to `POST` an API endpoint, use `post` like -

```php
use ChristianBrown\ApiClient\Exception\ExceptionInterface;

try {
    $data = $jsonApiRequestSender->post('url', ['query-string-1-key' => 'query-string-1-value'], [], ['body-key-1' => 'body-value-1']);
} catch (ExceptionInterface $e) {
    print $e->getMessage();
}
```



### `GET` example



If you need to `GET` data from an API endpoint, use `get` like -

```php
use ChristianBrown\ApiClient\Exception\ExceptionInterface;

try {
    $data = $jsonApiRequestSender->get('url', ['query-string-1-key' => 'query-string-1-value'], []);
} catch (ExceptionInterface $e) {
    print $e->getMessage();
}
```



## :rotating_light: Error handling

The main value-add of this library is that it catches Guzzle's transport-specific exceptions and
re-throws framework-agnostic ones. Every exception this library throws implements
`ChristianBrown\ApiClient\Exception\ExceptionInterface` (which extends `Throwable`), so a single
`catch` handles all of them:

```php
use ChristianBrown\ApiClient\Exception\ExceptionInterface;

try {
    $data = $jsonApiRequestSender->get('url');
} catch (ExceptionInterface $e) {
    // any failure from this library
    print $e->getMessage();
}
```

To handle specific failure modes, catch the narrower interfaces (all extend `ExceptionInterface`):

| Interface | Thrown when |
| --- | --- |
| `Exception\Request\ConnectExceptionInterface` | The request could not reach the host (DNS/connection failure). |
| `Exception\Response\BadResponseExceptionInterface` | The API returned a non-2xx status. Exposes `getRequest()`, `getResponse()`; the exception code is the HTTP status. |
| `Exception\Response\TooManyRedirectsExceptionInterface` | The request exceeded the redirect limit. |
| `Exception\Parse\ParseJsonExceptionInterface` | A JSON request body could not be encoded, or a JSON response could not be decoded. |
| `Exception\Parse\ParseXmlExceptionInterface` | An XML response could not be parsed. Exposes `getErrors()` (`LibXMLError[]`). |

Request/response exceptions expose the PSR-7 `getRequest()` (and `getResponse()` for response
errors); parse exceptions expose the failing `getMethod()`, `getUrl()`, and `getQueryStrings()`.



## :page_facing_up: License

Released under the [MIT License](LICENSE).



