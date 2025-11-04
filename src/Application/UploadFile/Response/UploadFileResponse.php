<?php

declare(strict_types=1);

namespace App\Application\UploadFile\Response;

final class UploadFileResponse
{
    public function __construct(
        public readonly string $originalName,
        public readonly string $filename,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $relativePath,
        public readonly string $publicUrl = '',
    ) {}

    public static function fromFile(
        string $originalName,
        string $filename,
        string $mimeType,
        int $size,
        string $relativePath,
        string $publicUrl = ''
    ): self {
        return new self($originalName, $filename, $mimeType, $size, $relativePath, $publicUrl);
    }


}
