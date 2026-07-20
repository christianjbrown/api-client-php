<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Exception\Response;

use ChristianBrown\ApiClient\Exception\Response\BadResponseException;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use GuzzleHttp\Exception\BadResponseException as GuzzleBadResponseException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(BadResponseException::class)]
final class BadResponseExceptionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test(): void
    {
        $requestUri = self::createStub(UriInterface::class);
        $requestUri->method('__toString')
            ->willReturn('https://test.com/');
        $request = self::createStub(RequestInterface::class);
        $request->method('getUri')
            ->willReturn($requestUri);

        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(42);
        $guzzleBadResponseException = self::createStub(GuzzleBadResponseException::class);
        $guzzleBadResponseException->method('getResponse')
            ->willReturn($response);

        $exception = new BadResponseException($request, $guzzleBadResponseException);
        self::assertSame($request, $exception->getRequest());
        self::assertSame($response, $exception->getResponse());
        self::assertSame(sprintf(BadResponseExceptionInterface::MESSAGE, 'https://test.com/', 42), $exception->getMessage());
        self::assertSame($guzzleBadResponseException, $exception->getPrevious());
    }

    /**
     * @throws Exception
     */
    public function testGetDecodedBodyReturnsTheDecodedArray(): void
    {
        $exception = new BadResponseException($this->createStubRequest(), $this->createStubGuzzleException('{"error":"invalid_grant"}'));

        self::assertSame(['error' => 'invalid_grant'], $exception->getDecodedBody());
    }

    /**
     * @throws Exception
     */
    public function testGetDecodedBodyReturnsNullWhenTheBodyIsNotAJsonArray(): void
    {
        $exception = new BadResponseException($this->createStubRequest(), $this->createStubGuzzleException('not-json'));

        self::assertNull($exception->getDecodedBody());
    }

    /**
     * @throws Exception
     */
    private function createStubGuzzleException(string $body): GuzzleBadResponseException
    {
        $stream = self::createStub(StreamInterface::class);
        $stream->method('__toString')
            ->willReturn($body);
        $response = self::createStub(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(400);
        $response->method('getBody')
            ->willReturn($stream);
        $guzzleBadResponseException = self::createStub(GuzzleBadResponseException::class);
        $guzzleBadResponseException->method('getResponse')
            ->willReturn($response);

        return $guzzleBadResponseException;
    }

    /**
     * @throws Exception
     */
    private function createStubRequest(): RequestInterface
    {
        $requestUri = self::createStub(UriInterface::class);
        $requestUri->method('__toString')
            ->willReturn('https://test.com/');
        $request = self::createStub(RequestInterface::class);
        $request->method('getUri')
            ->willReturn($requestUri);

        return $request;
    }
}
