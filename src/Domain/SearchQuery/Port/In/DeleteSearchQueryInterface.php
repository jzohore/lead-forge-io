<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\Port\In;

use App\Application\SearchQuery\DTO\SearchQueryDTO;

interface DeleteSearchQueryInterface
{
    public function __invoke(SearchQueryDTO $dto);
}