<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Transformer;

use ChristianBrown\ApiClient\Exception\AbstractException;
use ChristianBrown\ApiClient\Exception\Parse\AbstractParseException;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonException;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\RequestContext;
use ChristianBrown\ApiClient\Transformer\ArrayToJsonTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractException::class)]
#[CoversClass(AbstractParseException::class)]
#[CoversClass(ArrayToJsonTransformer::class)]
#[CoversClass(ParseJsonException::class)]
#[CoversClass(RequestContext::class)]
final class ArrayToJsonTransformerTest extends TestCase
{
    public function test(): void
    {
        $transformer = new ArrayToJsonTransformer();
        $actual = $transformer->transform(['test-data-key-1' => 'test-data-value-1'], new RequestContext('test-method', 'test-url', ['test-query-string-key-1' => 'test-query-string-value-1']));
        self::assertSame('{"test-data-key-1":"test-data-value-1"}', $actual);
    }

    public function testException(): void
    {
        $data = [];
        $data['self'] = &$data;

        $transformer = new ArrayToJsonTransformer();
        $parseJsonExceptionThrown = false;

        try {
            $transformer->transform($data, new RequestContext('test-method', 'test-url', ['test-query-string-key-1' => 'test-query-string-value-1']));
        } catch (ParseJsonExceptionInterface $e) {
            $parseJsonExceptionThrown = true;
            self::assertSame(sprintf(ParseJsonExceptionInterface::MESSAGE_SPRINTF, 'test-method', 'test-url', 'Recursion detected'), $e->getMessage());
            self::assertSame('test-method', $e->getMethod());
            self::assertSame('test-url', $e->getUrl());
            self::assertSame(['test-query-string-key-1' => 'test-query-string-value-1'], $e->getQueryStrings());
        }
        self::assertTrue($parseJsonExceptionThrown);
    }
}
