<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

final class RequestContext implements RequestContextInterface
{
    private string $method;

    /**
     * @var array<string, string>
     */
    private array $queryStrings;
    private string $url;

    /**
     * @param string                $method
     * @param string                $url
     * @param array<string, string> $queryStrings
     */
    public function __construct(string $method, string $url, array $queryStrings = [])
    {
        $this->method = $method;
        $this->queryStrings = $queryStrings;
        $this->url = $url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array<string, string>
     */
    public function getQueryStrings(): array
    {
        return $this->queryStrings;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
