<?php

declare(strict_types=1);

namespace App\Application\SearchQuery\UseCase;

use App\Application\SearchQuery\DTO\Message\ProcessSearchQueryMessage;
use App\Application\SearchQuery\DTO\Request\SearchQueryCreateRequest;
use App\Application\SearchQuery\DTO\Response\SearchQueryCreatedResponse;
use App\Application\SearchQuery\DTO\Response\SearchQueryErrorResponse;
use App\Application\User\DTO\Response\UserErrorResponse;
use App\Domain\SearchQuery\Entity\SearchQuery;
use App\Domain\SearchQuery\Port\In\CreateSearchQueryInterface;
use App\Domain\SearchQuery\Port\Out\SearchQueryRepositoryInterface;
use App\Domain\User\Port\Out\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class CreateSearchQueryUseCase implements CreateSearchQueryInterface
{
    public function __construct(
        private SearchQueryRepositoryInterface $searchQueryRepository,
        private ObjectMapperInterface $mapper,
        private LoggerInterface $logger,
        private UserRepositoryInterface $userRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(SearchQueryCreateRequest $request): UserErrorResponse|
    SearchQueryErrorResponse|SearchQueryCreatedResponse
    {
        try {
            $user = $this->userRepository->getById($request->userId);
            $email = $user->email;
            if (! $email) {
                $this->logger->warning('Email introuvable', [
                    'email' => $email,
                ]);

                return UserErrorResponse::userNotFound($email);
            }

            $searchQuery = new SearchQuery();
            $searchQuery->owner = $user;

            $this->mapper->map($request, $searchQuery);

            $this->searchQueryRepository->save($searchQuery);
            $this->messageBus->dispatch(new ProcessSearchQueryMessage(
                searchQueryId: (string) $searchQuery->id->toRfc4122(),
            ));
            $this->logger->info('Requête créer avec succès', [
                'requestId' => $searchQuery->id->toRfc4122(),
                'email' => $email,
                'query' => $searchQuery->query,
            ]);

            return SearchQueryCreatedResponse::fromEntity($searchQuery);
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de la création d\'une requête de lead', [
                'query' => $request->query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return SearchQueryErrorResponse::validationFailed([
                'system' => 'Une erreur est survenue lors de la création d\'une requête de lead ',
            ]);
        }
    }
}
