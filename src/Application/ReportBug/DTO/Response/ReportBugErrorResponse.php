<?php

declare(strict_types=1);

namespace App\Application\ReportBug\DTO\Response;

final readonly class ReportBugErrorResponse
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
            errors: $errors,
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
