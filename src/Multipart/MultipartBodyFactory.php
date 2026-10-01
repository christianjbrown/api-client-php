<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Multipart;

use GuzzleHttp\Psr7\MultipartStream;

use function array_map;

final class MultipartBodyFactory implements MultipartBodyFactoryInterface
{
    /**
     * @param array<int, MultipartPartInterface> $parts
     */
    public function create(array $parts): MultipartStream
    {
        return new MultipartStream(array_map(static fn (MultipartPartInterface $part): array => self::toElement($part), $parts));
    }

    /**
     * @param MultipartPartInterface $part The part to convert into Guzzle's element shape
     *
     * @return array{name: string, contents: string, filename: null|string, headers: array<string, string>}
     */
    private static function toElement(MultipartPartInterface $part): array
    {
        return [
            'name' => $part->getName(),
            'contents' => $part->getContents(),
            'filename' => $part->getFilename(),
            'headers' => $part->getHeaders(),
        ];
    }
}
