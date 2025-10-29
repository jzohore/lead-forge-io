<?php

declare(strict_types=1);

namespace App\Application\EarlyAccess\UseCase;

use App\Application\EarlyAccess\DTO\EarlyAccessDTO;
use App\Domain\EarlyAccess\Port\Out\EarlyAccessRepositoryInterface;
use App\Domain\EarlyAccess\Entity\EarlyAccess;
use App\Domain\EarlyAccess\Port\In\RestoreEarlyAccessInterface;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


final readonly class RestoreEarlyAccessUseCase implements RestoreEarlyAccessInterface
{
    public function __construct(
        private EarlyAccessRepositoryInterface $earlyAccessRepository,
        private ObjectMapperInterface $mapper,
    ) {}

    public function __invoke(EarlyAccessDTO $dto)
    {
                /** @var Uuid|null $id */
                $id = $dto->uuid;
                if (!$id) {
                    throw new NotFoundHttpException('EarlyAccess introuvable');
                }
        
                $earlyAccess = $this->earlyAccessRepository->getById($id);
                if (!$earlyAccess) {
                    throw new NotFoundHttpException('EarlyAccess introuvable');
                }
        
                // Annuler la suppression côté DTO pour restauration
                $dto->deletedAt = null;
        
                $this->mapper->map($dto, $earlyAccess);
                $this->earlyAccessRepository->save($earlyAccess);
    }
}