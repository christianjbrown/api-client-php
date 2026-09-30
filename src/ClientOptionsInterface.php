<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

/**
 * Transport settings for the underlying HTTP client. Guzzle's own defaults are 0 for both timeouts,
 * which means wait forever, so a request to an API that stops responding would hang until something
 * outside the process killed it. These defaults bound that; pass 0 to opt back out.
 */
interface ClientOptionsInterface
{
    public const float DEFAULT_CONNECT_TIMEOUT = 10.0;
    public const float DEFAULT_TIMEOUT = 30.0;

    /**
     * Seconds to wait for the connection to be established, or 0 to wait indefinitely.
     */
    public function getConnectTimeout(): float;

    /**
     * Seconds the whole request may take, including reading the response, or 0 to wait indefinitely.
     */
    public function getTimeout(): float;
}
