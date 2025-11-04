<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\Port\In;

use App\Application\SearchQuery\DTO\Request\SearchQueryCreateRequest;
use App\Application\SearchQuery\DTO\Response\SearchQueryCreatedResponse;
use App\Application\SearchQuery\DTO\Response\SearchQueryErrorResponse;
use App\Application\User\DTO\Response\UserErrorResponse;

interface CreateSearchQueryInterface
{
    public function __invoke(SearchQueryCreateRequest $request): UserErrorResponse|
    SearchQueryErrorResponse|SearchQueryCreatedResponse;
}
