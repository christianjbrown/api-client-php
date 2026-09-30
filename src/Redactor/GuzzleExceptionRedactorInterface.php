<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Redactor;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;

/**
 * Rebuilds a Guzzle exception around a redacted copy of its request. The original exception holds
 * the request exactly as it was sent, credentials included, so it must never end up in the
 * `getPrevious()` chain of an exception this library throws. The rebuilt exception keeps the
 * message, response and handler context, and drops its own previous exception, which could hold the
 * request as well.
 */
interface GuzzleExceptionRedactorInterface
{
    /**
     * @param BadResponseException $exception The exception Guzzle threw
     */
    public function redactBadResponseException(BadResponseException $exception): BadResponseException;

    /**
     * @param ConnectException $exception The exception Guzzle threw
     */
    public function redactConnectException(ConnectException $exception): ConnectException;

    /**
     * @param RequestException $exception The exception Guzzle threw
     */
    public function redactRequestException(RequestException $exception): RequestException;

    /**
     * @param TooManyRedirectsException $exception The exception Guzzle threw
     */
    public function redactTooManyRedirectsException(TooManyRedirectsException $exception): TooManyRedirectsException;
}
