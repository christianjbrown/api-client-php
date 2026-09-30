<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiClient;
use ChristianBrown\ApiClient\ApiClientContainerFactory;
use ChristianBrown\ApiClient\ApiClientContainerFactoryInterface;
use ChristianBrown\ApiClient\ApiRequestSender;
use ChristianBrown\ApiClient\ClientOptions;
use ChristianBrown\ApiClient\JsonApiRequestSender;
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactor;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformer;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformer;
use ChristianBrown\ApiClient\Transformer\StringToXmlDocTransformer;
use ChristianBrown\ApiClient\Transformer\XmlDocToStringTransformer;
use ChristianBrown\ApiClient\XmlApiRequestSender;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(ApiClient::class)]
#[CoversClass(ApiClientContainerFactory::class)]
#[CoversClass(ApiRequestSender::class)]
#[CoversClass(ArrayToJsonTransformer::class)]
#[CoversClass(ClientOptions::class)]
#[CoversClass(GuzzleExceptionRedactor::class)]
#[CoversClass(JsonApiRequestSender::class)]
#[CoversClass(JsonToArrayTransformer::class)]
#[CoversClass(RequestRedactor::class)]
#[CoversClass(StringToXmlDocTransformer::class)]
#[CoversClass(XmlApiRequestSender::class)]
#[CoversClass(XmlDocToStringTransformer::class)]
final class ApiClientTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test(): void
    {
        $apiClient = new ApiClient();

        $apiRequestSender = $apiClient->getApiRequestSender();
        self::assertInstanceOf(ApiRequestSender::class, $apiRequestSender);

        $jsonApiRequestSender = $apiClient->getJsonApiRequestSender();
        self::assertInstanceOf(JsonApiRequestSender::class, $jsonApiRequestSender);

        $xmlApiRequestSender = $apiClient->getXmlApiRequestSender();
        self::assertInstanceOf(XmlApiRequestSender::class, $xmlApiRequestSender);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testConstructorUsesInjectedContainerFactory(): void
    {
        $container = (new ApiClientContainerFactory(new ClientOptions()))->create();

        $containerFactory = self::createMock(ApiClientContainerFactoryInterface::class);
        $containerFactory->expects(self::once())
            ->method('create')
            ->willReturn($container);

        $apiClient = new ApiClient($containerFactory);

        self::assertInstanceOf(ApiRequestSender::class, $apiClient->getApiRequestSender());
    }
}
