<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiRequestSender;
use ChristianBrown\ApiClient\ApiRequestSenderInterface;
use ChristianBrown\ApiClient\FormApiRequestSenderInterface;
use ChristianBrown\ApiClient\JsonApiRequestSender;
use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\ApiClient\JsonFormApiRequestSenderInterface;
use ChristianBrown\ApiClient\JsonMultipartApiRequestSenderInterface;
use ChristianBrown\ApiClient\JsonReadApiRequestSenderInterface;
use ChristianBrown\ApiClient\JsonWriteApiRequestSenderInterface;
use ChristianBrown\ApiClient\MultipartApiRequestSenderInterface;
use ChristianBrown\ApiClient\ReadApiRequestSenderInterface;
use ChristianBrown\ApiClient\WriteApiRequestSenderInterface;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class SenderInterfaceSegregationTest extends TestCase
{
    public function testCombinedInterfacesCompose(): void
    {
        self::assertContains(ReadApiRequestSenderInterface::class, class_implements(ApiRequestSenderInterface::class));
        self::assertContains(JsonReadApiRequestSenderInterface::class, class_implements(JsonApiRequestSenderInterface::class));
    }

    /**
     * @param class-string $sender
     * @param class-string $narrowInterface
     */
    #[TestWith([ApiRequestSender::class, ReadApiRequestSenderInterface::class])]
    #[TestWith([ApiRequestSender::class, WriteApiRequestSenderInterface::class])]
    #[TestWith([ApiRequestSender::class, FormApiRequestSenderInterface::class])]
    #[TestWith([ApiRequestSender::class, MultipartApiRequestSenderInterface::class])]
    #[TestWith([JsonApiRequestSender::class, JsonReadApiRequestSenderInterface::class])]
    #[TestWith([JsonApiRequestSender::class, JsonWriteApiRequestSenderInterface::class])]
    #[TestWith([JsonApiRequestSender::class, JsonFormApiRequestSenderInterface::class])]
    #[TestWith([JsonApiRequestSender::class, JsonMultipartApiRequestSenderInterface::class])]
    public function testSenderImplementsNarrowInterface(string $sender, string $narrowInterface): void
    {
        self::assertContains($narrowInterface, class_implements($sender));
    }
}
