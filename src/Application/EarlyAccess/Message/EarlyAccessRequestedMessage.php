<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class EarlyAccessRequestedMessage
{
    public function __construct(
        public string $token,
        public string $email,
    ) {
    }
}
