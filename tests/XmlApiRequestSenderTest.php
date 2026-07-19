<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\Transformer\StringToXmlDocTransformerInterface;
use ChristianBrown\ApiClient\Transformer\XmlDocToStringTransformerInterface;
use ChristianBrown\ApiClient\RequestContext;
use ChristianBrown\ApiClient\XmlApiRequestSender;
use DOMDocument;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestContext::class)]
#[CoversClass(XmlApiRequestSender::class)]
final class XmlApiRequestSenderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGet(): void
    {
        $responseDoc = new DOMDocument();

        $apiRequestSender = self::createMock(ApiRequestSenderInterface::class);
        $apiRequestSender->expects(self::once())
            ->method('get')
            ->with(
                'test-url',
                ['test-query-string' => 'test-value'],
                ['test-header' => 'test-value']
            )
            ->willReturn('test-response');

        $responseTransformer = self::createMock(StringToXmlDocTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_GET, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn($responseDoc);

        $requestTransformer = self::createStub(XmlDocToStringTransformerInterface::class);
        $xmlApiRequestSender = new XmlApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $xmlApiRequestSender->get('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value']);

        self::assertSame($responseDoc, $actual);
    }

    /**
     * @throws Exception
     */
    public function testPost(): void
    {
        $requestDoc = new DOMDocument();
        $responseDoc = new DOMDocument();

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

        $responseTransformer = self::createMock(StringToXmlDocTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_POST, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn($responseDoc);

        $requestTransformer = self::createMock(XmlDocToStringTransformerInterface::class);
        $requestTransformer->expects(self::once())
            ->method('transform')
            ->with($requestDoc)
            ->willReturn('test-request-body');

        $xmlApiRequestSender = new XmlApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $xmlApiRequestSender->post('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value'], $requestDoc);

        self::assertSame($responseDoc, $actual);
    }

    /**
     * @throws Exception
     */
    public function testPostWithoutDocument(): void
    {
        $responseDoc = new DOMDocument();

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

        $responseTransformer = self::createMock(StringToXmlDocTransformerInterface::class);
        $responseTransformer->expects(self::once())
            ->method('transform')
            ->with(
                'test-response',
                self::equalTo(new RequestContext(ApiRequestSenderInterface::METHOD_POST, 'test-url', ['test-query-string' => 'test-value'])),
            )
            ->willReturn($responseDoc);

        $requestTransformer = self::createStub(XmlDocToStringTransformerInterface::class);
        $xmlApiRequestSender = new XmlApiRequestSender($apiRequestSender, $responseTransformer, $requestTransformer);
        $actual = $xmlApiRequestSender->post('test-url', ['test-query-string' => 'test-value'], ['test-header' => 'test-value']);

        self::assertSame($responseDoc, $actual);
    }
}
