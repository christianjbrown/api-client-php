<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Redactor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

use function array_reduce;

final class RequestRedactor implements RequestRedactorInterface
{
    /**
     * @var array<int, string>
     */
    private array $sensitiveHeaders;
    private StreamFactoryInterface $streamFactory;

    /**
     * @param array<int, string> $sensitiveHeaders
     */
    public function __construct(array $sensitiveHeaders, StreamFactoryInterface $streamFactory)
    {
        $this->sensitiveHeaders = $sensitiveHeaders;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @param RequestInterface $request The request to redact
     */
    public function redact(RequestInterface $request): RequestInterface
    {
        $requestWithoutHeaders = array_reduce($this->sensitiveHeaders, static fn (RequestInterface $carry, string $header): RequestInterface => self::withoutHeader($carry, $header), $request);

        /**
         * @var RequestInterface $redactedRequest
         */
        $redactedRequest = $requestWithoutHeaders->withBody($this->streamFactory->createStream());

        return $redactedRequest;
    }

    /**
     * @param RequestInterface $request The request to remove the header from
     * @param string           $header  The header name
     */
    private static function withoutHeader(RequestInterface $request, string $header): RequestInterface
    {
        /**
         * @var RequestInterface $requestWithoutHeader
         */
        $requestWithoutHeader = $request->withoutHeader($header);

        return $requestWithoutHeader;
    }
}
