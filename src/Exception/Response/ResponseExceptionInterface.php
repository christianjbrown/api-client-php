<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Exception\Response;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseExceptionInterface extends ExceptionInterface
{
    /**
     * The JSON-decoded response body, or null when the body is not a JSON array/object.
     * Lets callers inspect an error payload without touching the raw PSR-7 body.
     *
     * @return null|array<array-key, mixed>
     */
    public function getDecodedBody(): ?array;

    public function getRequest(): RequestInterface;

    public function getResponse(): ResponseInterface;
}
