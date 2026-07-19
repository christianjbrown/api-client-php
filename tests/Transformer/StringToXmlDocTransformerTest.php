<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Transformer;

use ChristianBrown\ApiClient\Exception\AbstractException;
use ChristianBrown\ApiClient\Exception\Parse\AbstractParseException;
use ChristianBrown\ApiClient\Exception\Parse\ParseXmlException;
use ChristianBrown\ApiClient\Exception\Parse\ParseXmlExceptionInterface;
use ChristianBrown\ApiClient\RequestContext;
use ChristianBrown\ApiClient\Transformer\StringToXmlDocTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

use function libxml_use_internal_errors;

#[CoversClass(AbstractException::class)]
#[CoversClass(AbstractParseException::class)]
#[CoversClass(ParseXmlException::class)]
#[CoversClass(RequestContext::class)]
#[CoversClass(StringToXmlDocTransformer::class)]
final class StringToXmlDocTransformerTest extends TestCase
{
    public function test(): void
    {
        $transformer = new StringToXmlDocTransformer();
        $actual = $transformer->transform('<xml><test-key-1>test-value-1</test-key-1></xml>', new RequestContext('test-method', 'test-url', ['test-query-string' => 'test-value']));

        $expected = <<<'XML'
            <?xml version="1.0"?>
            <xml><test-key-1>test-value-1</test-key-1></xml>

            XML;
        self::assertSame($expected, $actual->saveXML());
    }

    public function testException(): void
    {
        $transformer = new StringToXmlDocTransformer();

        $parseXmlExceptionThrown = false;

        try {
            $transformer->transform('<xml><test-invalid-xml', new RequestContext('test-method', 'test-url', ['test-query-string' => 'test-value']));
        } catch (ParseXmlExceptionInterface $e) {
            $parseXmlExceptionThrown = true;
            self::assertCount(2, $e->getErrors());
            self::assertSame('Couldn\'t find end of Start Tag test-invalid-xml line 1'."\n", $e->getErrors()[0]->message);
            self::assertSame('Premature end of data in tag xml line 1'."\n", $e->getErrors()[1]->message);
            self::assertSame('test-method', $e->getMethod());
            self::assertSame('test-url', $e->getUrl());
            self::assertSame(['test-query-string' => 'test-value'], $e->getQueryStrings());
        }
        self::assertTrue($parseXmlExceptionThrown);
    }

    #[TestWith(['<xml><valid/></xml>'])]
    #[TestWith(['<xml><test-invalid-xml'])]
    public function testRestoresLibxmlInternalErrorsState(string $xml): void
    {
        $previous = libxml_use_internal_errors(false);

        try {
            $transformer = new StringToXmlDocTransformer();

            try {
                $transformer->transform($xml, new RequestContext('test-method', 'test-url'));
            } catch (ParseXmlExceptionInterface) {
                // The failure path must restore state just like the success path.
            }

            // transform() flipped internal error handling on; it must have restored our prior value.
            self::assertFalse(libxml_use_internal_errors(false));
        } finally {
            libxml_use_internal_errors($previous);
        }
    }
}
