<?php

declare(strict_types=1);

namespace App\Domain\ReportBug\Port\In;

use App\Application\ReportBug\DTO\ReportBugDTO;

interface RestoreReportBugInterface
{
    public function __invoke(ReportBugDTO $dto);
}