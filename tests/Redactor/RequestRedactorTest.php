<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Redactor;

use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\Redactor\RequestRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactorInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(RequestRedactor::class)]
final class RequestRedactorTest extends TestCase
{
    /**
     * Every sensitive header is removed whatever its case, the body is replaced with the empty stream
     * from the factory, and everything else about the request survives. The original request keeps its
     * credentials, since it may still be in use.
     *
     * @throws Exception
     */
    public function testRedact(): void
    {
        $headers = [
            ApiRequestSenderInterface::HEADER_AUTHORIZATION => 'Bearer secret',
            ApiRequestSenderInterface::HEADER_PROXY_AUTHORIZATION => 'Basic proxy-secret',
            'APIKEY' => 'api-key-secret',
            'x-api-key' => 'x-api-key-secret',
            RequestRedactorInterface::HEADER_COOKIE => 'session=secret',
            'Accept' => 'application/json',
        ];
        $request = new Request(ApiRequestSenderInterface::METHOD_POST, 'test-url?test-query-string-key-1=test-query-string-value-1', $headers, 'refresh_token=secret');

        $emptyStream = Utils::streamFor('');
        $streamFactory = self::createMock(StreamFactoryInterface::class);
        $streamFactory->expects(self::once())
            ->method('createStream')
            ->willReturn($emptyStream);

        $redactor = new RequestRedactor(RequestRedactorInterface::SENSITIVE_HEADERS, $streamFactory);
        $redactedRequest = $redactor->redact($request);

        self::assertSame(['Accept' => ['application/json']], $redactedRequest->getHeaders());
        self::assertSame($emptyStream, $redactedRequest->getBody());
        self::assertSame(ApiRequestSenderInterface::METHOD_POST, $redactedRequest->getMethod());
        self::assertSame('test-url?test-query-string-key-1=test-query-string-value-1', $redactedRequest->getUri()->__toString());

        self::assertSame('Bearer secret', $request->getHeaderLine(ApiRequestSenderInterface::HEADER_AUTHORIZATION));
        self::assertSame('refresh_token=secret', $request->getBody()->__toString());
    }
}
