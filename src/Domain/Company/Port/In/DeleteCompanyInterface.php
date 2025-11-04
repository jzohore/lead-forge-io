<?php

declare(strict_types=1);

namespace App\Domain\Company\Port\In;

use App\Application\Company\DTO\CompanyDTO;

interface DeleteCompanyInterface
{
    public function __invoke(CompanyDTO $dto);
}