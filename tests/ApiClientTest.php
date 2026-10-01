<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiClient;
use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\ApiClient\XmlApiRequestSenderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ApiClient::class)]
final class ApiClientTest extends TestCase
{
    public function testExposesTheInjectedSenders(): void
    {
        $apiRequestSender = self::createStub(ApiRequestSenderInterface::class);
        $jsonApiRequestSender = self::createStub(JsonApiRequestSenderInterface::class);
        $xmlApiRequestSender = self::createStub(XmlApiRequestSenderInterface::class);

        $apiClient = new ApiClient($apiRequestSender, $jsonApiRequestSender, $xmlApiRequestSender);

        self::assertSame($apiRequestSender, $apiClient->getApiRequestSender());
        self::assertSame($jsonApiRequestSender, $apiClient->getJsonApiRequestSender());
        self::assertSame($xmlApiRequestSender, $apiClient->getXmlApiRequestSender());
    }
}
