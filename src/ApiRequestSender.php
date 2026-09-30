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
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactorInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Exception\TooManyRedirectsException as GuzzleTooManyRedirectsException;
use GuzzleHttp\Psr7\Request;

use function array_merge;
use function http_build_query;
use function sprintf;

final class ApiRequestSender implements ApiRequestSenderInterface
{
    private GuzzleExceptionRedactorInterface $exceptionRedactor;
    private ClientInterface $guzzle;

    public function __construct(ClientInterface $guzzle, GuzzleExceptionRedactorInterface $exceptionRedactor)
    {
        $this->guzzle = $guzzle;
        $this->exceptionRedactor = $exceptionRedactor;
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
     * @param string                $method              The HTTP method used for the request
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
    private function sendRequest(string $method, string $requestUrl, array $requestQueryStrings = [], array $requestHeaders = [], ?string $requestBody = null): string
    {
        $finalUrl = $requestUrl;
        if (!empty($requestQueryStrings)) {
            $requestQueryStringsFlat = http_build_query($requestQueryStrings, '', '&');
            $finalUrl = sprintf('%s?%s', $requestUrl, $requestQueryStringsFlat);
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
}
