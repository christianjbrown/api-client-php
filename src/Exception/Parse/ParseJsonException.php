<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Exception\Parse;

use ChristianBrown\ApiClient\RequestContextInterface;
use JsonException;

use function sprintf;

final class ParseJsonException extends AbstractParseException implements ParseJsonExceptionInterface
{
    private JsonException $jsonException;

    public function __construct(JsonException $jsonException, RequestContextInterface $context)
    {
        $this->jsonException = $jsonException;
        $message = sprintf(self::MESSAGE_SPRINTF, $context->getMethod(), $context->getUrl(), $jsonException->getMessage());
        parent::__construct($message, $context, $jsonException);
    }

    public function getJsonException(): JsonException
    {
        return $this->jsonException;
    }
}
