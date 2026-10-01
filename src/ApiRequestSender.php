<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

use ChristianBrown\ApiClient\Exception\Request\ConnectException;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\TransferException;
use ChristianBrown\ApiClient\Exception\Request\TransferExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseException;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsException;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use ChristianBrown\ApiClient\Multipart\MultipartBodyFactoryInterface;
use ChristianBrown\ApiClient\Multipart\MultipartPartInterface;
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactorInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Exception\TooManyRedirectsException as GuzzleTooManyRedirectsException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\StreamInterface;

use function array_filter;
use function array_merge;
use function http_build_query;
use function sprintf;
use function str_contains;
use function strcasecmp;

use const ARRAY_FILTER_USE_KEY;

final class ApiRequestSender implements ApiRequestSenderInterface
{
    private GuzzleExceptionRedactorInterface $exceptionRedactor;
    private ClientInterface $guzzle;
    private MultipartBodyFactoryInterface $multipartBodyFactory;

    public function __construct(ClientInterface $guzzle, GuzzleExceptionRedactorInterface $exceptionRedactor, MultipartBodyFactoryInterface $multipartBodyFactory)
    {
        $this->guzzle = $guzzle;
        $this->exceptionRedactor = $exceptionRedactor;
        $this->multipartBodyFactory = $multipartBodyFactory;
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function delete(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): string
    {
        return $this->sendRequest(self::METHOD_DELETE, $requestUrl, $requestQueryStrings, $requestHeaders);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function get(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = []): string
    {
        return $this->sendRequest(self::METHOD_GET, $requestUrl, $requestQueryStrings, $requestHeaders);
    }

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
    public function patch(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string
    {
        return $this->sendRequest(self::METHOD_PATCH, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBody);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function patchForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): string
    {
        return $this->sendFormRequest(self::METHOD_PATCH, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData);
    }

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function patchMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): string
    {
        return $this->sendMultipartRequest(self::METHOD_PATCH, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyParts);
    }

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
    public function post(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string
    {
        return $this->sendRequest(self::METHOD_POST, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBody);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function postForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): string
    {
        return $this->sendFormRequest(self::METHOD_POST, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData);
    }

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function postMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): string
    {
        return $this->sendMultipartRequest(self::METHOD_POST, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyParts);
    }

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
    public function put(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string
    {
        return $this->sendRequest(self::METHOD_PUT, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBody);
    }

    /**
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function putForm(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyFormData = []): string
    {
        return $this->sendFormRequest(self::METHOD_PUT, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyFormData);
    }

    /**
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function putMultipart(string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], array $requestBodyParts = []): string
    {
        return $this->sendMultipartRequest(self::METHOD_PUT, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBodyParts);
    }

    /**
     * Renders `$requestBodyFormData` as an `application/x-www-form-urlencoded` body and sends it with
     * `$method`, defaulting the content type header while letting a caller-supplied one win.
     *
     * @param string                $method              The HTTP method used for the request
     * @param string                $requestUrl          The request URL
     * @param array<string, string> $requestQueryStrings
     * @param array<string, string> $requestHeaders
     * @param array<string, string> $requestBodyFormData
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    private function sendFormRequest(string $method, string $requestUrl, array $requestQueryStrings, array $requestHeaders, array $requestBodyFormData): string
    {
        $requestBody = http_build_query($requestBodyFormData, '', '&');
        $requestHeaders = array_merge([self::HEADER_CONTENT_TYPE => self::CONTENT_TYPE_FORM_URLENCODED], $requestHeaders);

        return $this->sendRequest($method, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBody);
    }

    /**
     * Encodes `$requestBodyParts` as a `multipart/form-data` body and sends it with `$method`. The
     * `Content-Type` has to carry the body's boundary, so it always wins over one the caller passed.
     *
     * @param string                             $method              The HTTP method used for the request
     * @param string                             $requestUrl          The request URL
     * @param array<string, string>              $requestQueryStrings
     * @param array<string, string>              $requestHeaders
     * @param array<int, MultipartPartInterface> $requestBodyParts
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    private function sendMultipartRequest(string $method, string $requestUrl, array $requestQueryStrings, array $requestHeaders, array $requestBodyParts): string
    {
        $requestBody = $this->multipartBodyFactory->create($requestBodyParts);
        $requestHeaders = array_merge(self::withoutContentType($requestHeaders), [self::HEADER_CONTENT_TYPE => sprintf(self::CONTENT_TYPE_MULTIPART_SPRINTF, $requestBody->getBoundary())]);

        return $this->sendRequest($method, $requestUrl, $requestQueryStrings, $requestHeaders, $requestBody);
    }

    /**
     * @param string                      $method              The HTTP method used for the request
     * @param string                      $requestUrl          The request URL
     * @param array<string, string>       $requestQueryStrings
     * @param array<string, string>       $requestHeaders
     * @param null|StreamInterface|string $requestBody         The raw request body
     *
     * @throws ConnectExceptionInterface
     * @throws TransferExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    private function sendRequest(string $method, string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], null|StreamInterface|string $requestBody = null): string
    {
        $finalUrl = $requestUrl;
        if (!empty($requestQueryStrings)) {
            $requestQueryStringsFlat = http_build_query($requestQueryStrings, '', '&');
            // A URL that already carries a query string gets the new parameters appended to it,
            // rather than a second `?` that would fold them into the last existing value.
            $separator = str_contains($requestUrl, '?') ? '&' : '?';
            $finalUrl = sprintf('%s%s%s', $requestUrl, $separator, $requestQueryStringsFlat);
        }
        $request = new Request($method, $finalUrl, $requestHeaders, $requestBody);

        try {
            $response = $this->guzzle->send($request);
        } catch (GuzzleConnectException $exception) {
            $redactedException = $this->exceptionRedactor->redactConnectException($exception);

            throw new ConnectException($redactedException->getRequest(), $redactedException);
        } catch (GuzzleBadResponseException $exception) {
            $redactedException = $this->exceptionRedactor->redactBadResponseException($exception);

            throw new BadResponseException($redactedException->getRequest(), $redactedException);
        } catch (GuzzleTooManyRedirectsException $exception) {
            $redactedException = $this->exceptionRedactor->redactTooManyRedirectsException($exception);

            throw new TooManyRedirectsException($redactedException->getRequest(), $redactedException);
        } catch (GuzzleRequestException $exception) {
            $redactedException = $this->exceptionRedactor->redactRequestException($exception);

            throw new TransferException($redactedException->getRequest(), $redactedException);
        }

        $requestBody = $response->getBody();
        $contents = $requestBody->getContents();

        return $contents;
    }

    /**
     * Drops any `Content-Type` the caller passed, whatever its case, so it cannot end up alongside the
     * one this library has to set.
     *
     * @param array<string, string> $requestHeaders
     *
     * @return array<string, string>
     */
    private static function withoutContentType(array $requestHeaders): array
    {
        return array_filter($requestHeaders, static fn (int|string $name): bool => 0 !== strcasecmp((string) $name, self::HEADER_CONTENT_TYPE), ARRAY_FILTER_USE_KEY);
    }
}
