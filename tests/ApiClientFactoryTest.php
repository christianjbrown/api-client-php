<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiClient;
use ChristianBrown\ApiClient\ApiClientContainerFactory;
use ChristianBrown\ApiClient\ApiClientFactory;
use ChristianBrown\ApiClient\ApiRequestSender;
use ChristianBrown\ApiClient\ClientOptions;
use ChristianBrown\ApiClient\JsonApiRequestSender;
use ChristianBrown\ApiClient\Multipart\MultipartBodyFactory;
use ChristianBrown\ApiClient\Redactor\GuzzleExceptionRedactor;
use ChristianBrown\ApiClient\Redactor\RequestRedactor;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformer;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformer;
use ChristianBrown\ApiClient\Transformer\StringToXmlDocTransformer;
use ChristianBrown\ApiClient\Transformer\XmlDocToStringTransformer;
use ChristianBrown\ApiClient\XmlApiRequestSender;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ApiClientFactory::class)]
#[CoversClass(ApiClient::class)]
#[CoversClass(ApiClientContainerFactory::class)]
#[CoversClass(ApiRequestSender::class)]
#[CoversClass(ArrayToJsonTransformer::class)]
#[CoversClass(MultipartBodyFactory::class)]
#[CoversClass(ClientOptions::class)]
#[CoversClass(GuzzleExceptionRedactor::class)]
#[CoversClass(JsonApiRequestSender::class)]
#[CoversClass(JsonToArrayTransformer::class)]
#[CoversClass(RequestRedactor::class)]
#[CoversClass(StringToXmlDocTransformer::class)]
#[CoversClass(XmlApiRequestSender::class)]
#[CoversClass(XmlDocToStringTransformer::class)]
final class ApiClientFactoryTest extends TestCase
{
    public function testCreatesAnApiClientWithAllThreeSenders(): void
    {
        $apiClient = (new ApiClientFactory(new ClientOptions()))->create();

        self::assertInstanceOf(ApiClient::class, $apiClient);
        self::assertInstanceOf(ApiRequestSender::class, $apiClient->getApiRequestSender());
        self::assertInstanceOf(JsonApiRequestSender::class, $apiClient->getJsonApiRequestSender());
        self::assertInstanceOf(XmlApiRequestSender::class, $apiClient->getXmlApiRequestSender());
    }
}
