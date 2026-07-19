<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient;

interface RequestContextInterface
{
    public function getMethod(): string;

    /**
     * @return array<string, string>
     */
    public function getQueryStrings(): array;

    public function getUrl(): string;
}
