<?php

declare(strict_types=1);

namespace App\Application\SearchQuery\DTO\Message;

use App\Domain\SearchQuery\ValueObject\SearchQueryStatus;
use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

#[AsMessage('lead_async')]
final readonly class ProcessSearchQueryMessage
{
    public function __construct(
        public string $searchQueryId,
    ) {
    }
}
