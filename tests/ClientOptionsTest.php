<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ClientOptions;
use ChristianBrown\ApiClient\ClientOptionsInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClientOptions::class)]
final class ClientOptionsTest extends TestCase
{
    public function testDefaults(): void
    {
        $clientOptions = new ClientOptions();

        self::assertSame(ClientOptionsInterface::DEFAULT_TIMEOUT, $clientOptions->getTimeout());
        self::assertSame(ClientOptionsInterface::DEFAULT_CONNECT_TIMEOUT, $clientOptions->getConnectTimeout());
    }

    public function testGivenValues(): void
    {
        $clientOptions = new ClientOptions(5.5, 2.5);

        self::assertSame(5.5, $clientOptions->getTimeout());
        self::assertSame(2.5, $clientOptions->getConnectTimeout());
    }
}
