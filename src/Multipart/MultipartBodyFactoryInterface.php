<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Multipart;

use GuzzleHttp\Psr7\MultipartStream;

interface MultipartBodyFactoryInterface
{
    /**
     * Encodes `$parts` as a `multipart/form-data` body. The returned stream knows its own boundary,
     * which the request's `Content-Type` has to carry.
     *
     * @param array<int, MultipartPartInterface> $parts
     */
    public function create(array $parts): MultipartStream;
}
