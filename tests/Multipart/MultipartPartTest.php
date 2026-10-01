<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Multipart;

use ChristianBrown\ApiClient\Multipart\MultipartPart;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultipartPart::class)]
final class MultipartPartTest extends TestCase
{
    public function testField(): void
    {
        $part = new MultipartPart('test-name', 'test-contents');

        self::assertSame('test-name', $part->getName());
        self::assertSame('test-contents', $part->getContents());
        self::assertNull($part->getFilename());
        self::assertSame([], $part->getHeaders());
    }

    public function testFile(): void
    {
        $part = new MultipartPart('test-name', 'test-contents', 'test-file.png', ['Content-Type' => 'image/png']);

        self::assertSame('test-file.png', $part->getFilename());
        self::assertSame(['Content-Type' => 'image/png'], $part->getHeaders());
    }
}
