<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Multipart;

final class MultipartPart implements MultipartPartInterface
{
    private string $contents;
    private ?string $filename;

    /**
     * @var array<string, string>
     */
    private array $headers;
    private string $name;

    /**
     * @param string                $name     The form field name
     * @param string                $contents The part's contents
     * @param null|string           $filename The filename, or null for a plain field
     * @param array<string, string> $headers
     */
    public function __construct(string $name, string $contents, ?string $filename = null, array $headers = [])
    {
        $this->name = $name;
        $this->contents = $contents;
        $this->filename = $filename;
        $this->headers = $headers;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
