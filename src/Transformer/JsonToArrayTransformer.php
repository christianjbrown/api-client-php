<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Transformer;

use ChristianBrown\ApiClient\Exception\Parse\ParseJsonException;
use ChristianBrown\ApiClient\Exception\Parse\ParseJsonExceptionInterface;
use ChristianBrown\ApiClient\RequestContextInterface;
use JsonException;

use function is_array;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final class JsonToArrayTransformer implements JsonToArrayTransformerInterface
{
    /**
     * @param string                  $data
     * @param RequestContextInterface $context
     *
     * @throws ParseJsonExceptionInterface
     *
     * @return array<array-key, mixed>
     */
    public function transform(string $data, RequestContextInterface $context): array
    {
        try {
            $array = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new ParseJsonException($exception, $context);
        }

        if (!is_array($array)) {
            throw new ParseJsonException(new JsonException(sprintf(self::MESSAGE_NON_ARRAY_SPRINTF, $context->getMethod(), $context->getUrl())), $context);
        }

        return $array;
    }
}
