<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\TransferExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use ChristianBrown\ApiClient\Multipart\MultipartPartInterface;

interface JsonApiRequestSenderInterface
{
    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function delete(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): array;

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function get(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): array;

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patch(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array;

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patchForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array;

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function patchMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): array;

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function post(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array;

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function postForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array;

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function postMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): array;

    /**
     * @param string                       $requestUrl          The request URL
     * @param array<string, string>        $requestQueryStrings
     * @param array<string, string>        $requestHeaders
     * @param null|array<array-key, mixed> $requestBodyArray
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function put(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?array $requestBodyArray = null): array;

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function putForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): array;

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws ParseJsonExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function putMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): array;
}
