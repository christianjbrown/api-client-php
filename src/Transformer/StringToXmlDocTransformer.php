<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use ChristianBrown\ApiClient\Exception\Parse\ParseXmlException;
use ChristianBrown\ApiClient\Exception\Parse\ParseXmlExceptionInterface;
use ChristianBrown\ApiClient\RequestContextInterface;
use DOMDocument;

use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;

final class StringToXmlDocTransformer implements StringToXmlDocTransformerInterface
{
    /**
     * @throws ParseXmlExceptionInterface
     */
    public function transform(string $string, RequestContextInterface $context): DOMDocument
    {
        // libxml_use_internal_errors() mutates process-global state and returns its prior value;
        // capture it and restore it unconditionally so parsing here can't leak error handling into
        // the rest of the host application.
        $previousUseInternalErrors = libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $success = $doc->loadXML($string);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        if (!$success) {
            throw new ParseXmlException($errors, $context);
        }

        return $doc;
    }
}
