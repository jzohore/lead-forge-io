<?php

declare(strict_types=1);

namespace App\Domain\EarlyAccess\Port\In;

use App\Application\EarlyAccess\DTO\EarlyAccessDTO;

interface DeleteEarlyAccessInterface
{
    public function __invoke(EarlyAccessDTO $dto);
}