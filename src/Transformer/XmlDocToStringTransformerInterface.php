<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use DOMDocument;

interface XmlDocToStringTransformerInterface
{
    public function transform(DOMDocument $doc): string;
}
