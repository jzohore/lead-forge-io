<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\DTO\Response;

use App\Domain\EarlyAccess\Entity\EarlyAccess;

final readonly class EarlyAccessCreatedResponse
{
    public function __construct(
        public string $id,
        public string $email,
        public string $message,
        public bool $verificationEmailSent,
        public ?string $verificationExpiresAt = null,
    ) {
    }

    public static function fromEntity(
        EarlyAccess $earlyAccess,
        bool $emailSent = true
    ): self {
        return new self(
            id: $earlyAccess->id->toRfc4122(),
            email: $earlyAccess->email,
            message: sprintf(
                'Inscription réussie ! Un email de vérification a été envoyé à %s',
                $earlyAccess->email
            ),
            verificationEmailSent: $emailSent,
            verificationExpiresAt: $earlyAccess->validationTokenExpiresAt?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'email' => $this->email,
                'verificationEmailSent' => $this->verificationEmailSent,
                'verificationExpiresAt' => $this->verificationExpiresAt,
            ],
            'message' => $this->message,
        ];
    }
}
