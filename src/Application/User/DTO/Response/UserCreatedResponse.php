<?php

declare(strict_types=1);

namespace App\Application\User\DTO\Response;

use App\Domain\User\Entity\User;
use Symfony\Component\Uid\Uuid;

final readonly class UserCreatedResponse
{
    public function __construct(
        public Uuid $id,
        public string $email,
        public string $firstName,
        public string $lastName,
        public array $roles,
        public bool $isVerified,
        public string $message,
        public \DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * Factory depuis l'entité User.
     */
    public static function fromEntity(User $user, string $message = 'Utilisateur créé avec succès'): self
    {
        return new self(
            id: $user->id,
            email: $user->email,
            firstName: $user->firstName,
            lastName: $user->lastName,
            roles: $user->roles,
            isVerified: $user->isVerified,
            message: $message,
            createdAt: $user->createdAt,
        );
    }

    /**
     * Sérialisation pour API.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->toRfc4122(),
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'fullName' => sprintf('%s %s', $this->firstName, $this->lastName),
            'roles' => $this->roles,
            'isVerified' => $this->isVerified,
            'message' => $this->message,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * Sérialisation JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
