<?php

declare(strict_types=1);

namespace App\Domain\ReportBug\Port\In;

use App\Application\ReportBug\DTO\ReportBugDTO;
use App\Application\ReportBug\DTO\Request\CreateReportBugRequest;
use App\Application\ReportBug\DTO\Response\ReportBugCreatedResponse;
use App\Application\ReportBug\DTO\Response\ReportBugErrorResponse;
use App\Application\User\DTO\Response\UserErrorResponse;

interface CreateReportBugInterface
{
    public function __invoke(CreateReportBugRequest $request): ReportBugCreatedResponse|UserErrorResponse|ReportBugErrorResponse;
}
