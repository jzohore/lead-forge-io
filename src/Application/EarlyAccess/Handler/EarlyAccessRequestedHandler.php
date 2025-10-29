<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\Handler;

use App\Application\EarlyAccess\Message\EarlyAccessRequestedMessage;
use App\Application\Email\DTO\EmailTemplateDTO;
use App\Infrastructure\Shared\Service\MailerSettingsService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EarlyAccessRequestedHandler
{
    public function __construct(
        private MailerSettingsService $mailerSettingsService,
    ) {
    }

    public function __invoke(EarlyAccessRequestedMessage $message): void
    {
        $context = [
            'title' => '',
        ];

        $emailDTO = new EmailTemplateDTO();
        $emailDTO->toEmail = $message->email;
        $emailDTO->subject = 'Bienvenue chez LeadForge 🌱';
        $emailDTO->templatePath = 'emails/early_access/early_access_requested.html.twig';
        $emailDTO->context = $context;

        $this->mailerSettingsService->sendTemplatedEmail($emailDTO);
    }
}
