<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

use App\Domain\User\Entity\User;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

#[Map(target: User::class)]
final class UserDTO
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