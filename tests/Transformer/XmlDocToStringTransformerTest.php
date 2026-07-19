<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Transformer;

use ChristianBrown\ApiClient\Transformer\XmlDocToStringTransformer;
use DOMDocument;
use DOMException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(XmlDocToStringTransformer::class)]
final class XmlDocToStringTransformerTest extends TestCase
{
    /**
     * @throws DOMException
     */
    public function test(): void
    {
        $doc = new DOMDocument();
        $testElement = $doc->createElement('test-key-1', 'test-value-1');
        $doc->append($testElement);
        $transformer = new XmlDocToStringTransformer();
        $actual = $transformer->transform($doc);

        $expected = <<<'XML'
            <?xml version="1.0"?>
            <test-key-1>test-value-1</test-key-1>

            XML;
        self::assertSame($expected, $actual);
    }
}
