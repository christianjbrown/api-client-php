<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Exception\Request;

use ChristianBrown\ApiClient\Exception\Request\TransferException;
use ChristianBrown\ApiClient\Exception\Request\TransferExceptionInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

#[CoversClass(TransferException::class)]
final class TransferExceptionTest extends TestCase
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
        $previousException = new RuntimeException('test-previous-exception');
        $exception = new TransferException($request, $previousException);
        self::assertSame($request, $exception->getRequest());
        self::assertSame(sprintf(TransferExceptionInterface::MESSAGE, 'https://test.com/'), $exception->getMessage());
        self::assertSame($previousException, $exception->getPrevious());
    }
}
