<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Exception\Parse;

use ChristianBrown\ApiClient\RequestContextInterface;
use LibXMLError;

use function array_filter;
use function array_map;
use function array_values;
use function implode;
use function sprintf;

final class ParseXmlException extends AbstractParseException implements ParseXmlExceptionInterface
{
    /**
     * @var array<int, LibXMLError>
     */
    private array $errors = [];

    /**
     * @param array<int, mixed> $errors
     */
    public function __construct(array $errors, RequestContextInterface $context)
    {
        $this->errors = array_values(array_filter($errors, static fn (mixed $error): bool => $error instanceof LibXMLError));
        $message = self::generateMessage($this->errors, $context->getMethod(), $context->getUrl());
        parent::__construct($message, $context);
    }

    /**
     * @return array<int, LibXMLError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array<int, LibXMLError> $errors
     * @param string                  $method     The HTTP method used for the request
     * @param string                  $requestUrl The request URL
     */
    private function generateMessage(array $errors, string $method, string $requestUrl): string
    {
        $errorMessages = array_map(
            static fn (LibXMLError $error): string => sprintf(
                self::MESSAGE_ERROR_SPRINTF,
                $error->line,
                $error->message
            ),
            $errors
        );

        $message = sprintf(
            self::MESSAGE_SPRINTF,
            $method,
            $requestUrl,
            implode(self::MESSAGE_ERRORS_SEP, $errorMessages)
        );

        return $message;
    }
}
