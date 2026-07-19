<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\Exception\Request\ConnectExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\BadResponseExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\TooManyRedirectsExceptionInterface;
use ChristianBrown\ApiClient\JsonApiRequestSender;
use ChristianBrown\ApiClient\RequestContext;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformerInterface;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonApiRequestSender::class)]
#[CoversClass(RequestContext::class)]
final class JsonApiRequestSenderTest extends TestCase
{
    /**
     * @throws BadResponseExceptionInterface
     * @throws ConnectExceptionInterface
     * @throws Exception
     * @throws ParseJsonExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function testGet(): void
    {
        $apiRequestSender = self::createMock(ApiRequestSenderInterface::class);
        $apiRequestSender->expects(self::once())
            ->method('get')
            ->with(
                'test-url',
                ['test-query-string' => 'test-value'],
                ['test-header' => 'test-value']
            )
            ->willReturn('test-response');

        $responseTransformer = self::createMock(JsonToArrayTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_GET, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn(['test-response-array']);

        $requestTransformer = self::createStub(ArrayToJsonTransformerInterface::class);
        $jsonApiRequestSender = new JsonApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $jsonApiRequestSender->get('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value']);

        self::assertSame(['test-response-array'], $actual);
    }

    /**
     * @throws Exception
     * @throws ParseJsonExceptionInterface
     * @throws ConnectExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function testPost(): void
    {
        $apiRequestSender = self::createMock(ApiRequestSenderInterface::class);
        $apiRequestSender->expects(self::once())
            ->method('post')
            ->with(
                'test-url',
                ['test-query-string' => 'test-value'],
                ['test-header' => 'test-value'],
                'test-request-body'
            )
            ->willReturn('test-response');

        $responseTransformer = self::createMock(JsonToArrayTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_POST, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn(['test-response-array']);

        $requestTransformer = self::createMock(ArrayToJsonTransformerInterface::class);
        $requestTransformer->expects(self::once())
            ->method('transform')
            ->with(
                ['test-request-array'],
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_POST, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn('test-request-body');

        $jsonApiRequestSender = new JsonApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $jsonApiRequestSender->post('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value'], ['test-request-array']);

        self::assertSame(['test-response-array'], $actual);
    }

    /**
     * @throws Exception
     * @throws ParseJsonExceptionInterface
     * @throws ConnectExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function testPostForm(): void
    {
        $apiRequestSender = self::createMock(ApiRequestSenderInterface::class);
        $apiRequestSender->expects(self::once())
            ->method('postForm')
            ->with(
                'test-url',
                ['test-query-string' => 'test-value'],
                ['test-header' => 'test-value'],
                ['test-form-data-key' => 'test-form-data']
            )
            ->willReturn('test-response');

        $responseTransformer = self::createMock(JsonToArrayTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_POST, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn(['test-response-array']);

        $requestTransformer = self::createStub(ArrayToJsonTransformerInterface::class);

        $jsonApiRequestSender = new JsonApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $jsonApiRequestSender->postForm('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value'], ['test-form-data-key' => 'test-form-data']);

        self::assertSame(['test-response-array'], $actual);
    }

    /**
     * @throws Exception
     * @throws ParseJsonExceptionInterface
     * @throws ConnectExceptionInterface
     * @throws BadResponseExceptionInterface
     * @throws TooManyRedirectsExceptionInterface
     */
    public function testPostWithoutBody(): void
    {
        $apiRequestSender = self::createMock(ApiRequestSenderInterface::class);
        $apiRequestSender->expects(self::once())
            ->method('post')
            ->with(
                'test-url',
                ['test-query-string' => 'test-value'],
                ['test-header' => 'test-value'],
                null
            )
            ->willReturn('test-response');

        $responseTransformer = self::createMock(JsonToArrayTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_POST, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn(['test-response-array']);

        $requestTransformer = self::createStub(ArrayToJsonTransformerInterface::class);

        $jsonApiRequestSender = new JsonApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $jsonApiRequestSender->post('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value']);

        self::assertSame(['test-response-array'], $actual);
    }
}
