<?php

declare(strict_types=1);

namespace ChristianBrown\ApiClient\Multipart;

/**
 * One part of a `multipart/form-data` body: a plain field when it has no filename, or a file upload
 * when it has one.
 */
interface MultipartPartInterface
{
    public function getContents(): string;

    /**
     * The filename sent in the part's `Content-Disposition`, or null for a plain field.
     */
    public function getFilename(): ?string;

    /**
     * Extra headers for the part, such as its `Content-Type`.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array;

    public function getName(): string;
}
