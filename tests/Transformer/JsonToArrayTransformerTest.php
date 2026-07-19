<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Transformer;

use ChristianBrown\ApiClient\Exception\AbstractException;
use ChristianBrown\ApiClient\Exception\Parse\AbstractParseException;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonException;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\RequestContext;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformer;
use ChristianBrown\ApiClient\Transformer\JsonToArrayTransformerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractException::class)]
#[CoversClass(AbstractParseException::class)]
#[CoversClass(JsonToArrayTransformer::class)]
#[CoversClass(ParseJsonException::class)]
#[CoversClass(RequestContext::class)]
final class JsonToArrayTransformerTest extends TestCase
{
    public function test(): void
    {
        $transformer = new JsonToArrayTransformer();
        $actual = $transformer->transform('{"test-key-1": "test-value-1"}', new RequestContext('test-method', 'test-url', ['test-key-1' => 'test-value-1']));
        self::assertSame(['test-key-1' => 'test-value-1'], $actual);
    }

    public function testException(): void
    {
        $transformer = new JsonToArrayTransformer();
        $parseJsonExceptionThrown = false;

        try {
            $transformer->transform('{"test-invalid-json', new RequestContext('test-method', 'test-url', ['test-key-1' => 'test-value-1']));
        } catch (ParseJsonExceptionInterface $e) {
            $parseJsonExceptionThrown = true;
            self::assertSame(sprintf(ParseJsonExceptionInterface::MESSAGE_SPRINTF, 'test-method', 'test-url', 'Control character error, possibly incorrectly encoded'), $e->getMessage());
            self::assertSame('test-method', $e->getMethod());
            self::assertSame('test-url', $e->getUrl());
            self::assertSame(['test-key-1' => 'test-value-1'], $e->getQueryStrings());
        }
        self::assertTrue($parseJsonExceptionThrown);
    }

    public function testNonArrayException(): void
    {
        $transformer = new JsonToArrayTransformer();
        $parseJsonExceptionThrown = false;

        try {
            $transformer->transform('5', new RequestContext('test-method', 'test-url', ['test-key-1' => 'test-value-1']));
        } catch (ParseJsonExceptionInterface $e) {
            $parseJsonExceptionThrown = true;
            self::assertSame(sprintf(ParseJsonExceptionInterface::MESSAGE_SPRINTF, 'test-method', 'test-url', sprintf(JsonToArrayTransformerInterface::MESSAGE_NON_ARRAY_SPRINTF, 'test-method', 'test-url')), $e->getMessage());
            self::assertSame('test-method', $e->getMethod());
            self::assertSame('test-url', $e->getUrl());
            self::assertSame(['test-key-1' => 'test-value-1'], $e->getQueryStrings());
        }
        self::assertTrue($parseJsonExceptionThrown);
    }
}
