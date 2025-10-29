<?php

declare(strict_types=1);

namespace App\Domain\Profil\Port\In;

use App\Application\Profil\DTO\ProfilDTO;

interface UpdateProfilInterface
{
    public function __invoke(ProfilDTO $dto);
}