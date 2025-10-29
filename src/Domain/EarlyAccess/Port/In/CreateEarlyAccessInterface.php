<?php

declare(strict_types=1);

namespace App\Domain\EarlyAccess\Port\In;

use App\Application\EarlyAccess\DTO\EarlyAccessDTO;
use App\Application\EarlyAccess\DTO\Request\CreateEarlyAccessRequest;
use App\Application\EarlyAccess\DTO\Response\EarlyAccessCreatedResponse;
use App\Application\EarlyAccess\DTO\Response\EarlyAccessErrorResponse;

interface CreateEarlyAccessInterface
{
    public function __invoke(CreateEarlyAccessRequest $request): EarlyAccessCreatedResponse|EarlyAccessErrorResponse;
}
