<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Service;

use App\Application\Email\DTO\EmailTemplateDTO;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerSettingsService
{
    public string $protocol {
        get {
            $server_port = $_SERVER['SERVER_PORT'] ?? null;

            return $this->protocol = ((! empty($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']) || 443 == $server_port)
                ? 'https://' : 'http://';
        }
    }

    public string $url {
        get {
            $http_host = $_SERVER['HTTP_HOST'] ?? null;

            return $this->protocol.$http_host;
        }
    }

    public function __construct(
        #[Autowire('%env(MEMBER_EMAIL_SENDER)%')]
        private readonly string $emailSender,
        #[Autowire('%env(MEMBER_EMAIL_NAME)%')]
        private readonly string $nameMemberSender,
        private readonly MailerInterface $mailer
    ) {
    }

    public function createTemplatedEmail(
        string $toEmail,
        string $subject,
        string $templatePath,
        array $context = []
    ): TemplatedEmail {
        return new TemplatedEmail()
            ->from(new Address($this->emailSender, $this->nameMemberSender))
            ->to(new Address($toEmail))
            ->subject($subject)
            ->htmlTemplate($templatePath)
            ->context($context);
    }

    // ✅ NOUVELLE méthode : crée avec DTO SANS envoyer
    public function createTemplatedEmailFromDTO(EmailTemplateDTO $dto): TemplatedEmail
    {
        return $this->createTemplatedEmail(
            $dto->toEmail,
            $dto->subject,
            $dto->templatePath,
            $dto->context
        );
    }

    // ✅ Méthode pour envoyer directement (usage simple)
    public function sendTemplatedEmail(EmailTemplateDTO $dto): void
    {
        $email = $this->createTemplatedEmailFromDTO($dto);
        $this->mailer->send($email);
    }
}
