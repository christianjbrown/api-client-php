<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiRequestSender;
use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonException;
use ChristianBrown\ApiClient\Exception\Parse\ParseXmlException;
use ChristianBrown\ApiClient\Exception\Request\ConnectException;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseException;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsException;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException as GuzzleTooManyRedirectsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;

#[CoversClass(ApiRequestSender::class)]
#[CoversClass(ParseJsonException::class)]
#[CoversClass(ParseXmlException::class)]
#[CoversClass(ConnectException::class)]
#[CoversClass(BadResponseException::class)]
#[CoversClass(TooManyRedirectsException::class)]
final class ApiRequestSenderTest extends TestCase
{
    /**
     * @param string                            $function              The sender method to invoke
     * @param array<int, mixed>                 $functionArgs
     * @param string                            $expectedRequestMethod The expected HTTP request method
     * @param array<string, array<int, string>> $expectedHeaders
     * @param string                            $expectedRequestBody   The expected request body
     * @param string                            $expectedRequestUrl    The expected request URL
     * @param string                            $responseBodyContent   The stubbed response body content
     *
     * @throws ConnectException
     * @throws Exception
     * @throws ParseJsonException
     * @throws ParseXmlException
     * @throws BadResponseException
     * @throws TooManyRedirectsException
     */
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    public function testBadResponseException(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $guzzleBadResponseException = self::createStub(GuzzleBadResponseException::class);

        $requestSender = $this->getRequestSenderForException($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $guzzleBadResponseException);
        $responseExceptionThrown = false;

        try {
            $requestSender->{$function}(...$functionArgs);
        } catch (BadResponseExceptionInterface $e) {
            $responseExceptionThrown = true;

            $request = $e->getRequest();
            self::assertSame($expectedRequestMethod, $request->getMethod());
            self::assertSame($expectedHeaders, $request->getHeaders());
            $requestBody = $request->getBody();
            $requestBody->rewind();
            self::assertSame($expectedRequestBody, $requestBody->getContents());
            self::assertSame($expectedRequestUrl, $request->getUri()->__toString());

            self::assertSame($guzzleBadResponseException, $e->getPrevious());
        }

        self::assertTrue($responseExceptionThrown);
    }

    /**
     * @param string                            $function              The sender method to invoke
     * @param array<int, mixed>                 $functionArgs
     * @param string                            $expectedRequestMethod The expected HTTP request method
     * @param array<string, array<int, string>> $expectedHeaders
     * @param string                            $expectedRequestBody   The expected request body
     * @param string                            $expectedRequestUrl    The expected request URL
     * @param string                            $responseBodyContent   The stubbed response body content
     *
     * @throws ConnectException
     * @throws Exception
     * @throws ParseJsonException
     * @throws ParseXmlException
     * @throws BadResponseException
     * @throws TooManyRedirectsException
     */
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    public function testConnectException(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $guzzleConnectException = self::createStub(GuzzleConnectException::class);

        $requestSender = $this->getRequestSenderForException($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $guzzleConnectException);
        $connectExceptionThrown = false;

        try {
            $requestSender->{$function}(...$functionArgs);
        } catch (ConnectExceptionInterface $e) {
            $connectExceptionThrown = true;

            $request = $e->getRequest();
            self::assertSame($expectedRequestMethod, $request->getMethod());
            self::assertSame($expectedHeaders, $request->getHeaders());
            $body = $request->getBody();
            $body->rewind();
            self::assertSame($expectedRequestBody, $body->getContents());
            self::assertSame($expectedRequestUrl, $request->getUri()->__toString());

            self::assertSame($guzzleConnectException, $e->getPrevious());
        }

        self::assertTrue($connectExceptionThrown);
    }

    /**
     * A failed request must not leak credentials: the request stored on the thrown exception has its
     * `Authorization` and `Proxy-Authorization` headers redacted, while the in-flight request that was
     * actually sent (asserted inside the Guzzle send callback) keeps them intact and every
     * non-sensitive header (e.g. `Accept`) survives on the stored request.
     *
     * @throws ConnectException
     * @throws Exception
     * @throws ParseJsonException
     * @throws ParseXmlException
     * @throws BadResponseException
     * @throws TooManyRedirectsException
     */
    public function testRedactsSensitiveHeadersFromStoredRequest(): void
    {
        $requestHeaders = [
            ApiRequestSenderInterface::HEADER_AUTHORIZATION => 'Bearer secret',
            ApiRequestSenderInterface::HEADER_PROXY_AUTHORIZATION => 'Basic proxy-secret',
            'Accept' => 'application/json',
        ];
        $sentHeaders = [
            ApiRequestSenderInterface::HEADER_AUTHORIZATION => ['Bearer secret'],
            ApiRequestSenderInterface::HEADER_PROXY_AUTHORIZATION => ['Basic proxy-secret'],
            'Accept' => ['application/json'],
        ];

        $guzzleConnectException = self::createStub(GuzzleConnectException::class);
        $requestSender = $this->getRequestSenderForException(ApiRequestSenderInterface::METHOD_GET, $sentHeaders, '', 'test-url', 'test-response', $guzzleConnectException);
        $connectExceptionThrown = false;

        try {
            $requestSender->get('test-url', [], $requestHeaders);
        } catch (ConnectExceptionInterface $e) {
            $connectExceptionThrown = true;

            $request = $e->getRequest();
            self::assertFalse($request->hasHeader(ApiRequestSenderInterface::HEADER_AUTHORIZATION));
            self::assertSame('', $request->getHeaderLine(ApiRequestSenderInterface::HEADER_AUTHORIZATION));
            self::assertFalse($request->hasHeader(ApiRequestSenderInterface::HEADER_PROXY_AUTHORIZATION));
            self::assertSame('', $request->getHeaderLine(ApiRequestSenderInterface::HEADER_PROXY_AUTHORIZATION));
            self::assertSame('application/json', $request->getHeaderLine('Accept'));
        }

        self::assertTrue($connectExceptionThrown);
    }

    /**
     * @param string                            $function              The sender method to invoke
     * @param array<int, mixed>                 $functionArgs
     * @param string                            $expectedRequestMethod The expected HTTP request method
     * @param array<string, array<int, string>> $expectedHeaders
     * @param string                            $expectedRequestBody   The expected request body
     * @param string                            $expectedRequestUrl    The expected request URL
     * @param string                            $responseBodyContent   The stubbed response body content
     *
     * @throws ConnectException
     * @throws Exception
     * @throws ParseJsonException
     * @throws ParseXmlException
     * @throws BadResponseException
     * @throws TooManyRedirectsException
     */
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['get', ['test-url', [], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', [], [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => 'application/json'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => ['application/json']], 'test-body-key-1=test-body-value-1', 'test-url', 'test-response'])]
    public function testSuccess(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $guzzle = self::createStub(ClientInterface::class);
        $guzzle->method('send')
            ->willReturnCallback(
                static function (RequestInterface $request) use ($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent) {
                    self::assertSame($expectedRequestMethod, $request->getMethod());
                    self::assertSame($expectedHeaders, $request->getHeaders());
                    self::assertSame($expectedRequestBody, $request->getBody()->getContents());
                    self::assertSame($expectedRequestUrl, $request->getUri()->__toString());

                    $responseSteam = self::createStub(StreamInterface::class);
                    $responseSteam->method('getContents')
                        ->willReturn($responseBodyContent);
                    $response = self::createStub(ResponseInterface::class);
                    $response->method('getStatusCode')
                        ->willReturn(42);
                    $response->method('getBody')
                        ->willReturn($responseSteam);

                    return $response;
                }
            );

        $requestSender = new ApiRequestSender($guzzle);
        $actual = $requestSender->{$function}(...$functionArgs);
        self::assertSame('test-response', $actual);
    }

    /**
     * @param string                            $function              The sender method to invoke
     * @param array<int, mixed>                 $functionArgs
     * @param string                            $expectedRequestMethod The expected HTTP request method
     * @param array<string, array<int, string>> $expectedHeaders
     * @param string                            $expectedRequestBody   The expected request body
     * @param string                            $expectedRequestUrl    The expected request URL
     * @param string                            $responseBodyContent   The stubbed response body content
     *
     * @throws ConnectException
     * @throws Exception
     * @throws ParseJsonException
     * @throws ParseXmlException
     * @throws BadResponseException
     * @throws TooManyRedirectsException
     */
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    public function testTooManyRedirectsException(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $response = self::createStub(ResponseInterface::class);
        $guzzleTooManyRedirectsException = self::createStub(GuzzleTooManyRedirectsException::class);
        $guzzleTooManyRedirectsException->method('getResponse')
            ->willReturn($response);

        $requestSender = $this->getRequestSenderForException($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $guzzleTooManyRedirectsException);
        $tooManyRedirectsExceptionThrown = false;

        try {
            $requestSender->{$function}(...$functionArgs);
        } catch (TooManyRedirectsExceptionInterface $e) {
            $tooManyRedirectsExceptionThrown = true;

            $request = $e->getRequest();
            self::assertSame($expectedRequestMethod, $request->getMethod());
            self::assertSame($expectedHeaders, $request->getHeaders());
            $requestBody = $request->getBody();
            $requestBody->rewind();
            self::assertSame($expectedRequestBody, $requestBody->getContents());
            self::assertSame($expectedRequestUrl, $request->getUri()->__toString());

            self::assertSame($guzzleTooManyRedirectsException, $e->getPrevious());
        }

        self::assertTrue($tooManyRedirectsExceptionThrown);
    }

    /**
     * @param string                            $expectedRequestMethod The expected HTTP request method
     * @param array<string, array<int, string>> $expectedHeaders
     * @param string                            $expectedRequestBody   The expected request body
     * @param string                            $expectedRequestUrl    The expected request URL
     * @param string                            $responseBodyContent   The stubbed response body content
     * @param null|Throwable                    $throwException        The exception the Guzzle client should throw
     *
     * @throws Exception
     */
    private function getRequestSenderForException(string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent, ?Throwable $throwException = null): ApiRequestSenderInterface
    {
        $guzzle = self::createStub(ClientInterface::class);
        $guzzle->method('send')
            ->willReturnCallback(
                static function (RequestInterface $request) use ($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $throwException): void {
                    self::assertSame($expectedRequestMethod, $request->getMethod());
                    self::assertSame($expectedHeaders, $request->getHeaders());
                    self::assertSame($expectedRequestBody, $request->getBody()->getContents());
                    self::assertSame($expectedRequestUrl, $request->getUri()->__toString());

                    $responseSteam = self::createStub(StreamInterface::class);
                    $responseSteam->method('getContents')
                        ->willReturn($responseBodyContent);
                    $response = self::createStub(ResponseInterface::class);
                    $response->method('getStatusCode')
                        ->willReturn(42);
                    $response->method('getBody')
                        ->willReturn($responseSteam);

                    if (null !== $throwException) {
                        throw $throwException;
                    }
                }
            );

        $requestSender = new ApiRequestSender($guzzle);

        return $requestSender;
    }
}
