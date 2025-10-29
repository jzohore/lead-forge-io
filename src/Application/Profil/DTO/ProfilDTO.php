<?php

declare(strict_types=1);

namespace App\Application\Profil\DTO;

use App\Domain\Profil\Entity\Profil;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[Map(target: Profil::class)]
final class ProfilDTO
{
    /**
     * @var Uuid|null
     */
    #[Map(target: 'id')]
    public ?Uuid $uuid = null;

    /**
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $updatedAt = null;

    /**
     * @var DateTimeImmutable|null
     */
    public ?DateTimeImmutable $deletedAt = null;

}