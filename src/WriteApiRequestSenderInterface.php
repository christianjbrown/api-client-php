<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\TransferExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;

interface WriteApiRequestSenderInterface
{
    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param null|string           $requestBody         The raw request body
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function patch(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string;

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param null|string           $requestBody         The raw request body
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function post(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string;

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param null|string           $requestBody         The raw request body
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function put(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string;
}
