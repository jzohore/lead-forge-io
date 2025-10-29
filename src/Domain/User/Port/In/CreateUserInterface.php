<?php

declare(strict_types=1);

namespace App\Domain\User\Port\In;

use App\Application\User\DTO\Request\CreateUserRequest;
use App\Application\User\DTO\Response\UserCreatedResponse;
use App\Application\User\DTO\Response\UserErrorResponse;
use App\Application\User\DTO\UserDTO;

interface CreateUserInterface
{
    public function __invoke(CreateUserRequest $request): UserCreatedResponse|UserErrorResponse;
}
