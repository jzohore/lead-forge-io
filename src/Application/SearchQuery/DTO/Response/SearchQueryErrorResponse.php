<?php

declare(strict_types=1);

namespace App\Application\SearchQuery\DTO\Response;

final readonly class SearchQueryErrorResponse
{
    private function __construct(
        public string $type,
        public string $message,
        public ?array $errors = null,
    ) {
    }

    public static function validationFailed(array $errors): self
    {
        return new self(
            type: 'validation_failed',
            message: 'Les données fournies sont invalides',
            errors: $errors
        );
    }

    public static function businessError(string $message, ?array $errors = null): self
    {
        return new self(
            type: 'business_error',
            message: $message,
            errors: $errors
        );
    }

    public static function notFound(string $entity = 'Resource'): self
    {
        return new self(
            type: 'not_found',
            message: sprintf('%s introuvable', $entity)
        );
    }

    public static function serverError(string $message = 'Erreur serveur'): self
    {
        return new self(
            type: 'server_error',
            message: $message
        );
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
            'message' => $this->message,
        ];

        if (null !== $this->errors) {
            $data['errors'] = $this->errors;
        }

        return $data;
    }
}
