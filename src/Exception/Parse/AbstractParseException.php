<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Exception\Parse;

use ChristianBrown\ApiClient\RequestContextInterface;
use RuntimeException;
use Throwable;

abstract class AbstractParseException extends RuntimeException implements ParseExceptionInterface
{
    private RequestContextInterface $context;

    public function __construct(string $message, RequestContextInterface $context, ?Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, 0, $previous);
    }

    final public function getMethod(): string
    {
        return $this->context->getMethod();
    }

    /**
     * @return array<string, string>
     */
    final public function getQueryStrings(): array
    {
        return $this->context->getQueryStrings();
    }

    final public function getUrl(): string
    {
        return $this->context->getUrl();
    }
}
