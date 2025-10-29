<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\UseCase;

use App\Application\EarlyAccess\DTO\Request\CreateEarlyAccessRequest;
use App\Application\EarlyAccess\DTO\Response\EarlyAccessCreatedResponse;
use App\Application\EarlyAccess\DTO\Response\EarlyAccessErrorResponse;
use App\Application\EarlyAccess\Message\EarlyAccessRequestedMessage;
use App\Domain\EarlyAccess\Entity\EarlyAccess;
use App\Domain\EarlyAccess\Port\In\CreateEarlyAccessInterface;
use App\Domain\EarlyAccess\Port\Out\EarlyAccessRepositoryInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class CreateEarlyAccessUseCase implements CreateEarlyAccessInterface
{
    public function __construct(
        private EarlyAccessRepositoryInterface $earlyAccessRepository,
        private ObjectMapperInterface $mapper,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CreateEarlyAccessRequest $request): EarlyAccessCreatedResponse|EarlyAccessErrorResponse
    {
        if ($this->earlyAccessRepository->getEmail($request->getNormalizedEmail())) {
            return EarlyAccessErrorResponse::emailAlreadyExists($request->email);
        }
        $earlyAccess = new EarlyAccess();
        $this->mapper->map($request, $earlyAccess);
        $this->earlyAccessRepository->save($earlyAccess);
        $this->messageBus->dispatch(
            new EarlyAccessRequestedMessage(
                token: $earlyAccess->validationToken,
                email: $earlyAccess->email
            ),
            [new DispatchAfterCurrentBusStamp()]
        );

        return EarlyAccessCreatedResponse::fromEntity($earlyAccess);
    }
}
