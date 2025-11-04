<?php

declare(strict_types=1);

namespace App\Application\ReportBug\DTO\Response;

use App\Domain\ReportBug\Entity\ReportBug;

final readonly class ReportBugCreatedResponse
{
    public function __construct(
        public string $id,
        public string $email,
        public string $message,
        public ?string $messageSuccess,
        public \DateTimeImmutable $createdAt,
    ) {
    }

    public static function fromEntity(ReportBug $reportBug, string $messageSuccess = 'Merci pour votre retour !'): self
    {
        return new self(
            id: $reportBug->id->toRfc4122(),
            email: $reportBug->owner->email,
            message: $reportBug->message,
            messageSuccess: $messageSuccess,
            createdAt: $reportBug->createdAt,
        );
    }
}
