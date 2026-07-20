<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use ChristianBrown\ApiClient\Exception\Parse\ParseXmlExceptionInterface;
use ChristianBrown\ApiClient\RequestContextInterface;
use DOMDocument;

interface StringToXmlDocTransformerInterface
{
    /**
     * @throws ParseXmlExceptionInterface
     */
    public function transform(string $string, RequestContextInterface $context): DOMDocument;
}
