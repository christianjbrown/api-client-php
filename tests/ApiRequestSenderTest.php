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
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactor;
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactorInterface;
use ChristianBrown\ApiClient\Redactor\RequestRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactorInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException as GuzzleTooManyRedirectsException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
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
#[CoversClass(GuzzleExceptionRedactor::class)]
#[CoversClass(RequestRedactor::class)]
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
    #[TestWith(['delete', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_DELETE, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patch', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PATCH, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['put', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PUT, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patchForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PATCH, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['putForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PUT, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    public function testBadResponseException(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $guzzleBadResponseException = self::createStub(GuzzleBadResponseException::class);

        $redactedRequest = new Request(ApiRequestSenderInterface::METHOD_GET, 'test-redacted-url');
        $redactedException = new GuzzleBadResponseException('test-message', $redactedRequest, new Response(401));
        $exceptionRedactor = self::createMock(GuzzleExceptionRedactorInterface::class);
        $exceptionRedactor->expects(self::once())
            ->method('redactBadResponseException')
            ->with($guzzleBadResponseException)
            ->willReturn($redactedException);

        $requestSender = self::getRequestSenderForException($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $exceptionRedactor, $guzzleBadResponseException);
        $responseExceptionThrown = false;

        try {
            $requestSender->{$function}(...$functionArgs);
        } catch (BadResponseExceptionInterface $e) {
            $responseExceptionThrown = true;

            self::assertSame($redactedRequest, $e->getRequest());
            self::assertSame($redactedException, $e->getPrevious());
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
    #[TestWith(['delete', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_DELETE, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patch', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PATCH, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['put', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PUT, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patchForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PATCH, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['putForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PUT, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    public function testConnectException(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $guzzleConnectException = self::createStub(GuzzleConnectException::class);

        $redactedRequest = new Request(ApiRequestSenderInterface::METHOD_GET, 'test-redacted-url');
        $redactedException = new GuzzleConnectException('test-message', $redactedRequest);
        $exceptionRedactor = self::createMock(GuzzleExceptionRedactorInterface::class);
        $exceptionRedactor->expects(self::once())
            ->method('redactConnectException')
            ->with($guzzleConnectException)
            ->willReturn($redactedException);

        $requestSender = self::getRequestSenderForException($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $exceptionRedactor, $guzzleConnectException);
        $connectExceptionThrown = false;

        try {
            $requestSender->{$function}(...$functionArgs);
        } catch (ConnectExceptionInterface $e) {
            $connectExceptionThrown = true;

            self::assertSame($redactedRequest, $e->getRequest());
            self::assertSame($redactedException, $e->getPrevious());
        }

        self::assertTrue($connectExceptionThrown);
    }

    /**
     * A failed request must not leak credentials anywhere in the exception chain. Guzzle is made to
     * throw the way it really does, with the request exactly as sent, and the real redactors are wired
     * in. Neither the request on the thrown exception nor the one on its `getPrevious()` may carry a
     * sensitive header or the form body, and the chain must end there.
     *
     * @throws ConnectException
     * @throws Exception
     * @throws ParseJsonException
     * @throws ParseXmlException
     * @throws BadResponseException
     * @throws TooManyRedirectsException
     */
    public function testExceptionChainCarriesNoCredentials(): void
    {
        $requestHeaders = [
            ApiRequestSenderInterface::HEADER_AUTHORIZATION => 'Basic secret',
            RequestRedactorInterface::HEADER_API_KEY => 'api-key-secret',
            'Accept' => 'application/json',
        ];

        $guzzle = self::createStub(ClientInterface::class);
        $guzzle->method('send')
            ->willReturnCallback(
                static function (RequestInterface $request): void {
                    throw new GuzzleConnectException('test-message', $request);
                }
            );
        $exceptionRedactor = new GuzzleExceptionRedactor(new RequestRedactor(RequestRedactorInterface::SENSITIVE_HEADERS, new HttpFactory()));
        $requestSender = new ApiRequestSender($guzzle, $exceptionRedactor);
        $connectExceptionThrown = false;

        try {
            $requestSender->postForm('test-url', [], $requestHeaders, ['refresh_token' => 'refresh-token-secret']);
        } catch (ConnectExceptionInterface $e) {
            $connectExceptionThrown = true;

            $previous = $e->getPrevious();
            self::assertInstanceOf(GuzzleConnectException::class, $previous);
            self::assertNull($previous->getPrevious());

            $expectedHeaders = [
                ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED],
                'Accept' => ['application/json'],
            ];
            self::assertSame($expectedHeaders, $e->getRequest()->getHeaders());
            self::assertSame('', $e->getRequest()->getBody()->__toString());
            self::assertSame($expectedHeaders, $previous->getRequest()->getHeaders());
            self::assertSame('', $previous->getRequest()->getBody()->__toString());
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
    #[TestWith(['delete', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_DELETE, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['get', ['test-url', [], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url', 'test-response'])]
    #[TestWith(['patch', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PATCH, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['put', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PUT, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patchForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PATCH, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['putForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PUT, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
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

        $requestSender = new ApiRequestSender($guzzle, self::createStub(GuzzleExceptionRedactorInterface::class));
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
    #[TestWith(['delete', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_DELETE, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['get', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1']], ApiRequestSenderInterface::METHOD_GET, [['test-header-1']], '', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patch', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PATCH, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['post', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_POST, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['put', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], 'test-body'], ApiRequestSenderInterface::METHOD_PUT, [['test-header-1']], 'test-body', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['patchForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PATCH, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['postForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_POST, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    #[TestWith(['putForm', ['test-url', ['test-query-string-key-1' => 'test-query-string-value-1'], ['test-header-1'], ['test-body-key-1' => 'test-body-value-1']], ApiRequestSenderInterface::METHOD_PUT, [ApiRequestSenderInterface::HEADER_CONTENT_TYPE => [ApiRequestSenderInterface::CONTENT_TYPE_FORM_URLENCODED], ['test-header-1']], 'test-body-key-1=test-body-value-1', 'test-url?test-query-string-key-1=test-query-string-value-1', 'test-response'])]
    public function testTooManyRedirectsException(string $function, array $functionArgs, string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent): void
    {
        $guzzleTooManyRedirectsException = self::createStub(GuzzleTooManyRedirectsException::class);

        $redactedRequest = new Request(ApiRequestSenderInterface::METHOD_GET, 'test-redacted-url');
        $redactedException = new GuzzleTooManyRedirectsException('test-message', $redactedRequest, new Response(302));
        $exceptionRedactor = self::createMock(GuzzleExceptionRedactorInterface::class);
        $exceptionRedactor->expects(self::once())
            ->method('redactTooManyRedirectsException')
            ->with($guzzleTooManyRedirectsException)
            ->willReturn($redactedException);

        $requestSender = self::getRequestSenderForException($expectedRequestMethod, $expectedHeaders, $expectedRequestBody, $expectedRequestUrl, $responseBodyContent, $exceptionRedactor, $guzzleTooManyRedirectsException);
        $tooManyRedirectsExceptionThrown = false;

        try {
            $requestSender->{$function}(...$functionArgs);
        } catch (TooManyRedirectsExceptionInterface $e) {
            $tooManyRedirectsExceptionThrown = true;

            self::assertSame($redactedRequest, $e->getRequest());
            self::assertSame($redactedException, $e->getPrevious());
        }

        self::assertTrue($tooManyRedirectsExceptionThrown);
    }

    /**
     * @param string                            $expectedRequestMethod The expected HTTP request method
     * @param array<string, array<int, string>> $expectedHeaders
     * @param string                            $expectedRequestBody   The expected request body
     * @param string                            $expectedRequestUrl    The expected request URL
     * @param string                            $responseBodyContent   The stubbed response body content
     * @param GuzzleExceptionRedactorInterface  $exceptionRedactor     The redactor the sender hands Guzzle exceptions to
     * @param null|Throwable                    $throwException        The exception the Guzzle client should throw
     *
     * @throws Exception
     */
    private static function getRequestSenderForException(string $expectedRequestMethod, array $expectedHeaders, string $expectedRequestBody, string $expectedRequestUrl, string $responseBodyContent, GuzzleExceptionRedactorInterface $exceptionRedactor, ?Throwable $throwException = null): ApiRequestSenderInterface
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

        $requestSender = new ApiRequestSender($guzzle, $exceptionRedactor);

        return $requestSender;
    }
}
