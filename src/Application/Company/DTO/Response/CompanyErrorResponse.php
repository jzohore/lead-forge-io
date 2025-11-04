<?php

namespace App\Application\Company\DTO\Response;

final readonly class CompanyErrorResponse
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

    public static function sirenAlreadyExists(string $siren, string $companyName): self
    {
        return new self(
            type: 'siren_already_exists',
            message: sprintf('Le siren "%s" de la société "%s" est déjà utilisé', $siren, $companyName),
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

    public static function notFound(string $entity = 'Company'): self
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
