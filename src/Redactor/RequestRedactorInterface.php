<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Redactor;

use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use Psr\Http\Message\RequestInterface;

interface RequestRedactorInterface
{
    public const string HEADER_API_KEY = 'apikey';
    public const string HEADER_COOKIE = 'Cookie';
    public const string HEADER_X_API_KEY = 'X-Api-Key';

    /**
     * Headers that carry a credential in the APIs this library is used against. PSR-7 header names
     * are case-insensitive, so one spelling of each is enough.
     *
     * @var array<int, string>
     */
    public const array SENSITIVE_HEADERS = [
        ApiRequestSenderInterface::HEADER_AUTHORIZATION,
        ApiRequestSenderInterface::HEADER_PROXY_AUTHORIZATION,
        self::HEADER_API_KEY,
        self::HEADER_COOKIE,
        self::HEADER_X_API_KEY,
    ];

    /**
     * Returns a copy of `$request` that is safe to keep on an exception: the sensitive headers are
     * removed and the body is emptied, since a body can hold a credential too (an OAuth refresh token
     * or client secret in a form post). The original request is left untouched.
     *
     * @param RequestInterface $request The request to redact
     */
    public function redact(RequestInterface $request): RequestInterface;
}
