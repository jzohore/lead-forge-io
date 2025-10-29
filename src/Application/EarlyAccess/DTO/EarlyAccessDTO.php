<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\DTO;

use App\Domain\EarlyAccess\Entity\EarlyAccess;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[Map(target: EarlyAccess::class)]
final class EarlyAccessDTO
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