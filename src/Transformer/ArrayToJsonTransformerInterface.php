<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\RequestContextInterface;

interface ArrayToJsonTransformerInterface
{
    /**
     * @param array<array-key, mixed> $data
     *
     * @throws ParseJsonExceptionInterface
     */
    public function transform(array $data, RequestContextInterface $context): string;
}
