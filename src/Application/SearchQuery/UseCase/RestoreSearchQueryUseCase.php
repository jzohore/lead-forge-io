<?php

declare(strict_types=1);

namespace App\Application\SearchQuery\UseCase;

use App\Application\SearchQuery\DTO\SearchQueryDTO;
use App\Domain\SearchQuery\Port\Out\SearchQueryRepositoryInterface;
use App\Domain\SearchQuery\Entity\SearchQuery;
use App\Domain\SearchQuery\Port\In\RestoreSearchQueryInterface;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


final readonly class RestoreSearchQueryUseCase implements RestoreSearchQueryInterface
{
    public function __construct(
        private SearchQueryRepositoryInterface $searchQueryRepository,
        private ObjectMapperInterface $mapper,
    ) {}

    public function __invoke(SearchQueryDTO $dto)
    {
                /** @var Uuid|null $id */
                $id = $dto->uuid;
                if (!$id) {
                    throw new NotFoundHttpException('SearchQuery introuvable');
                }
        
                $searchQuery = $this->searchQueryRepository->getById($id);
                if (!$searchQuery) {
                    throw new NotFoundHttpException('SearchQuery introuvable');
                }
        
                // Annuler la suppression côté DTO pour restauration
                $dto->deletedAt = null;
        
                $this->mapper->map($dto, $searchQuery);
                $this->searchQueryRepository->save($searchQuery);
    }
}