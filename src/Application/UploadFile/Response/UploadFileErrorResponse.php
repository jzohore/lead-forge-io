<?php

declare(strict_types=1);

namespace App\Application\UploadFile\Response;

final readonly class UploadFileErrorResponse
{
    private function __construct(
        public string $type,
        public string $message,
        public ?array $errors = null,
    ) {
    }

    public static function cannotMove(array $errors): self
    {
        return new self(
            type: 'uploaded_failed',
            message: 'Impossible d\'enregistrer le fichier.',
            errors: $errors,
        );
    }

    public static function invalidMimeType(array $errors): self
    {
        return new self(
            type: 'invalid_mime_type',
            message: sprintf('Type MIME non autorisé : %s (autorisé: %s)', $errors['mime'], implode(
                ', ',
                $errors['allowed_mime_types']
            )),
            errors: $errors,
        );
    }

    public static function fileTooLarge(array $errors): self
    {
        return new self(
            type: 'file_too_large',
            message: sprintf(
                'Fichier trop volumineux (%d octets). Limite : %d octets',
                $errors['size'],
                $errors['max']
            ),
            errors: $errors,
        );
    }
}
