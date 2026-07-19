<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use DOMDocument;

final class XmlDocToStringTransformer implements XmlDocToStringTransformerInterface
{
    public function transform(DOMDocument $doc): string
    {
        // saveXML() only returns false for an unbuildable document, which cannot occur for an
        // already-parsed DOMDocument, so the string cast is always exercised.
        return (string) $doc->saveXML();
    }
}
