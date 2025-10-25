<?php

namespace App\Application\User\DTO;

use App\Domain\User\Entity\User;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: User::class)]
class CreateUserDTO
{

}
