<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Exception\Parse;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonException;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\RequestContext;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParseJsonException::class)]
#[CoversClass(RequestContext::class)]
final class ParseJsonExceptionTest extends TestCase
{
    public function test(): void
    {
        $jsonException = new JsonException('test-error-message');

        $exception = new ParseJsonException($jsonException, new RequestContext('test-method', 'test-url', ['test-query-string' => 'test-value']));
        self::assertSame($jsonException, $exception->getJsonException());
        self::assertSame($jsonException, $exception->getPrevious());
        self::assertSame('test-method', $exception->getMethod());
        self::assertSame('test-url', $exception->getUrl());
        self::assertSame(['test-query-string' => 'test-value'], $exception->getQueryStrings());
        self::assertSame(sprintf(ParseJsonExceptionInterface::MESSAGE_SPRINTF, 'test-method', 'test-url', 'test-error-message'), $exception->getMessage());
    }
}
