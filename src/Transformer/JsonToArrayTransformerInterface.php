<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\RequestContextInterface;

interface JsonToArrayTransformerInterface
{
    public const string MESSAGE_NON_ARRAY_SPRINTF = 'Decoded JSON is not an array whilst %sing to %s';

    /**
     * @throws ParseJsonExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function transform(string $data, RequestContextInterface $context): array;
}
