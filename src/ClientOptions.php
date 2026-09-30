<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

final class ClientOptions implements ClientOptionsInterface
{
    private float $connectTimeout;
    private float $timeout;

    public function __construct(float $timeout = self::DEFAULT_TIMEOUT, float $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT)
    {
        $this->timeout = $timeout;
        $this->connectTimeout = $connectTimeout;
    }

    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }
}
