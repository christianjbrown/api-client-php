<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests;

use ChristianBrown\ApiClient\ApiClientContainerFactory;
use ChristianBrown\ApiClient\ApiClientInterface;
use ChristianBrown\ApiClient\ClientOptionsInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(ApiClientContainerFactory::class)]
final class ApiClientContainerFactoryTest extends TestCase
{
    /**
     * The timeouts must reach the Guzzle client the container actually builds. Asserting on the client
     * rather than on the definition proves the wiring, so a renamed option key or a dropped argument
     * fails here instead of silently leaving Guzzle's wait-forever default in place.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGuzzleClientUsesClientOptions(): void
    {
        $clientOptions = self::createStub(ClientOptionsInterface::class);
        $clientOptions->method('getTimeout')
            ->willReturn(5.5);
        $clientOptions->method('getConnectTimeout')
            ->willReturn(2.5);

        $container = (new ApiClientContainerFactory($clientOptions))->create();
        $guzzle = $container->get(ApiClientInterface::SERVICE_GUZZLE_CLIENT);

        self::assertInstanceOf(Client::class, $guzzle);
        self::assertSame(5.5, $guzzle->getConfig(RequestOptions::TIMEOUT));
        self::assertSame(2.5, $guzzle->getConfig(RequestOptions::CONNECT_TIMEOUT));
    }
}
