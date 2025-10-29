<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\DTO\Response;

final readonly class EarlyAccessErrorResponse
{
    public function __construct(
        public string $error,
        public string $message,
        public int $statusCode = 400,
        public ?array $violations = null,
    ) {
    }

    public static function invalidToken(): self
    {
        return new self(
            error: 'INVALID_TOKEN',
            message: 'Le lien de vérification est invalide ou a expiré',
            statusCode: 400,
        );
    }

    public static function emailAlreadyExists(string $email): self
    {
        return new self(
            error: 'EMAIL_EXISTS',
            message: sprintf('Un compte existe déjà avec l\'email %s', $email),
            statusCode: 409,
        );
    }

    public static function emailNotFound(string $email): self
    {
        return new self(
            error: 'EMAIL_NOT_FOUND',
            message: sprintf('Aucun compte trouvé avec l\'email %s', $email),
            statusCode: 404,
        );
    }

    public static function alreadyVerified(): self
    {
        return new self(
            error: 'ALREADY_VERIFIED',
            message: 'Cet email a déjà été vérifié',
            statusCode: 400,
        );
    }

    public static function validationFailed(array $violations): self
    {
        return new self(
            error: 'VALIDATION_FAILED',
            message: 'Les données fournies sont invalides',
            statusCode: 422,
            violations: $violations,
        );
    }

    public function toArray(): array
    {
        $response = [
            'success' => false,
            'error' => $this->error,
            'message' => $this->message,
        ];

        if ($this->violations) {
            $response['violations'] = $this->violations;
        }

        return $response;
    }
}
