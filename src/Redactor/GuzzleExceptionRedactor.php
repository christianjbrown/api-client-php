<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Redactor;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;

final class GuzzleExceptionRedactor implements GuzzleExceptionRedactorInterface
{
    private RequestRedactorInterface $requestRedactor;

    public function __construct(RequestRedactorInterface $requestRedactor)
    {
        $this->requestRedactor = $requestRedactor;
    }

    /**
     * @param BadResponseException $exception The exception Guzzle threw
     */
    public function redactBadResponseException(BadResponseException $exception): BadResponseException
    {
        $request = $this->requestRedactor->redact($exception->getRequest());

        return new BadResponseException($exception->getMessage(), $request, $exception->getResponse(), null, $exception->getHandlerContext());
    }

    /**
     * @param ConnectException $exception The exception Guzzle threw
     */
    public function redactConnectException(ConnectException $exception): ConnectException
    {
        $request = $this->requestRedactor->redact($exception->getRequest());

        return new ConnectException($exception->getMessage(), $request, null, $exception->getHandlerContext());
    }

    /**
     * @param RequestException $exception The exception Guzzle threw
     */
    public function redactRequestException(RequestException $exception): RequestException
    {
        $request = $this->requestRedactor->redact($exception->getRequest());

        return new RequestException($exception->getMessage(), $request, $exception->getResponse(), null, $exception->getHandlerContext());
    }

    /**
     * @param TooManyRedirectsException $exception The exception Guzzle threw
     */
    public function redactTooManyRedirectsException(TooManyRedirectsException $exception): TooManyRedirectsException
    {
        $request = $this->requestRedactor->redact($exception->getRequest());

        return new TooManyRedirectsException($exception->getMessage(), $request, $exception->getResponse(), null, $exception->getHandlerContext());
    }
}
