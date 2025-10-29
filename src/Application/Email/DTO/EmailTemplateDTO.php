<?php

namespace App\Application\Email\DTO;

class EmailTemplateDTO
{
    /**
     * @var string|null
     */
    public ?string $subject = null;

    /**
     * @var string|null
     */
    public ?string $templatePath = null;

    /**
     * @var string|null
     */
    public ?string $toEmail = null;

    /**
     * @var array|null
     */
    public ?array $context = null;
}
