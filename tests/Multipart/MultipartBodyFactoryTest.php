<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Tests\Multipart;

use ChristianBrown\ApiClient\Multipart\MultipartBodyFactory;
use ChristianBrown\ApiClient\Multipart\MultipartPartInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(MultipartBodyFactory::class)]
final class MultipartBodyFactoryTest extends TestCase
{
    /**
     * A plain field and a file part both reach the encoded body, the file with its filename and its
     * own headers.
     *
     * @throws Exception
     */
    public function testCreate(): void
    {
        $field = self::createStub(MultipartPartInterface::class);
        $field->method('getName')
            ->willReturn('test-field');
        $field->method('getContents')
            ->willReturn('test-value');
        $field->method('getFilename')
            ->willReturn(null);
        $field->method('getHeaders')
            ->willReturn([]);

        $file = self::createStub(MultipartPartInterface::class);
        $file->method('getName')
            ->willReturn('test-file');
        $file->method('getContents')
            ->willReturn('test-file-contents');
        $file->method('getFilename')
            ->willReturn('test-file.png');
        $file->method('getHeaders')
            ->willReturn(['Content-Type' => 'image/png']);

        $body = (new MultipartBodyFactory())->create([$field, $file]);
        $boundary = $body->getBoundary();

        $expected = sprintf('--%s', $boundary)."\r\n"
        .'Content-Disposition: form-data; name="test-field"'."\r\n"
        .'Content-Length: 10'."\r\n"
        ."\r\n"
        .'test-value'."\r\n"
        .sprintf('--%s', $boundary)."\r\n"
        .'Content-Type: image/png'."\r\n"
        .'Content-Disposition: form-data; name="test-file"; filename="test-file.png"'."\r\n"
        .'Content-Length: 18'."\r\n"
        ."\r\n"
        .'test-file-contents'."\r\n"
        .sprintf('--%s--', $boundary)."\r\n";
        self::assertSame($expected, $body->__toString());
    }
}
