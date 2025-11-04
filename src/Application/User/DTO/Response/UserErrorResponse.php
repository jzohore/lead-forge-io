<?php

declare(strict_types=1);

namespace App\Application\User\DTO\Response;

final readonly class UserErrorResponse
{
    private function __construct(
        public string $type,
        public string $message,
        public ?array $errors = null,
    ) {
    }

    public static function emailAlreadyExists(string $email): self
    {
        return new self(
            type: 'email_already_exists',
            message: sprintf('L\'email "%s" est déjà utilisé', $email),
        );
    }

    public static function userNotFound(string $userId): self
    {
        return new self(
            type: 'user_not_found',
            message: sprintf('Utilisateur avec l\'ID "%s" introuvable', $userId),
        );
    }

    public static function invalidPassword(): self
    {
        return new self(
            type: 'invalid_password',
            message: 'Le mot de passe actuel est incorrect',
        );
    }

    public static function invalidToken(): self
    {
        return new self(
            type: 'invalid_token',
            message: 'Le token de vérification est invalide ou expiré',
        );
    }

    public static function validationFailed(array $errors): self
    {
        return new self(
            type: 'validation_failed',
            message: 'Les données fournies sont invalides',
            errors: $errors,
        );
    }

    public static function insufficientCredits(int $required, int $available): self
    {
        return new self(
            type: 'insufficient_credits',
            message: sprintf(
                'Crédits insuffisants. Requis: %d, Disponibles: %d',
                $required,
                $available
            ),
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
