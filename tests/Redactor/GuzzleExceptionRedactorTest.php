<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Redactor;

use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactorInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

#[CoversClass(GuzzleExceptionRedactor::class)]
final class GuzzleExceptionRedactorTest extends TestCase
{
    private const array HANDLER_CONTEXT = ['errno' => 28, 'error' => 'test-handler-error'];

    /**
     * @throws Exception
     */
    public function testRedactBadResponseException(): void
    {
        $request = self::createSentRequest();
        $redactedRequest = self::createRedactedRequest();
        $response = new Response(401);
        $exception = new BadResponseException('test-message', $request, $response, new RuntimeException(), self::HANDLER_CONTEXT);

        $redactor = new GuzzleExceptionRedactor($this->createRequestRedactor($request, $redactedRequest));
        $actual = $redactor->redactBadResponseException($exception);

        self::assertSame('test-message', $actual->getMessage());
        self::assertSame($redactedRequest, $actual->getRequest());
        self::assertSame($response, $actual->getResponse());
        self::assertSame(self::HANDLER_CONTEXT, $actual->getHandlerContext());
        self::assertNull($actual->getPrevious());
    }

    /**
     * @throws Exception
     */
    public function testRedactConnectException(): void
    {
        $request = self::createSentRequest();
        $redactedRequest = self::createRedactedRequest();
        $exception = new ConnectException('test-message', $request, new RuntimeException(), self::HANDLER_CONTEXT);

        $redactor = new GuzzleExceptionRedactor($this->createRequestRedactor($request, $redactedRequest));
        $actual = $redactor->redactConnectException($exception);

        self::assertSame('test-message', $actual->getMessage());
        self::assertSame($redactedRequest, $actual->getRequest());
        self::assertSame(self::HANDLER_CONTEXT, $actual->getHandlerContext());
        self::assertNull($actual->getPrevious());
    }

    /**
     * @throws Exception
     */
    public function testRedactTooManyRedirectsException(): void
    {
        $request = self::createSentRequest();
        $redactedRequest = self::createRedactedRequest();
        $response = new Response(302);
        $exception = new TooManyRedirectsException('test-message', $request, $response, new RuntimeException(), self::HANDLER_CONTEXT);

        $redactor = new GuzzleExceptionRedactor($this->createRequestRedactor($request, $redactedRequest));
        $actual = $redactor->redactTooManyRedirectsException($exception);

        self::assertSame('test-message', $actual->getMessage());
        self::assertSame($redactedRequest, $actual->getRequest());
        self::assertSame($response, $actual->getResponse());
        self::assertSame(self::HANDLER_CONTEXT, $actual->getHandlerContext());
        self::assertNull($actual->getPrevious());
    }

    private static function createRedactedRequest(): RequestInterface
    {
        return new Request(ApiRequestSenderInterface::METHOD_GET, 'test-url');
    }

    /**
     * @param RequestInterface $request         The request the redactor should be handed
     * @param RequestInterface $redactedRequest The request the redactor returns
     *
     * @throws Exception
     */
    private function createRequestRedactor(RequestInterface $request, RequestInterface $redactedRequest): RequestRedactorInterface
    {
        $requestRedactor = $this->createMock(RequestRedactorInterface::class);
        $requestRedactor->expects(self::once())
            ->method('redact')
            ->with($request)
            ->willReturn($redactedRequest);

        return $requestRedactor;
    }

    private static function createSentRequest(): RequestInterface
    {
        return new Request(ApiRequestSenderInterface::METHOD_GET, 'test-url', [ApiRequestSenderInterface::HEADER_AUTHORIZATION => 'Bearer secret']);
    }
}
