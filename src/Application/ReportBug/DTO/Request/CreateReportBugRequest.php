<?php

namespace App\Application\ReportBug\DTO\Request;

use App\Domain\ReportBug\Entity\ReportBug;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: ReportBug::class)]
final class CreateReportBugRequest
{
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 10,
        max: 2500,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères'
    )]
    public ?string $message = null;


    public ?string $filename = null;

    #[Assert\Length(max: 50)]
    #[Assert\Choice(
        choices: [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'text/plain',
        ],
        message: 'Type de fichier non autorisé'
    )]
    public ?string $mimeType = null;

    #[Assert\Uuid(
        message: 'L\'identifiant utilisateur doit être un UUID v4 valide',
        versions: [4]
    )]
    public ?Uuid $userId = null;
}
