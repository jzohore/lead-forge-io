<?php

declare(strict_types=1);

namespace App\Application\Profil\UseCase;

use App\Application\Profil\DTO\ProfilDTO;
use App\Domain\Profil\Port\Out\ProfilRepositoryInterface;
use App\Domain\Profil\Entity\Profil;
use App\Domain\Profil\Port\In\DeleteProfilInterface;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


final readonly class DeleteProfilUseCase implements DeleteProfilInterface
{
    public function __construct(
        private ProfilRepositoryInterface $profilRepository,
        private ObjectMapperInterface $mapper,
    ) {}

    public function __invoke(ProfilDTO $dto)
    {
                /** @var Uuid|null $id */
                $id = $dto->uuid;
                if (!$id) {
                    throw new NotFoundHttpException('Profil introuvable');
                }
        
                $profil = $this->profilRepository->getById($id);
                if (!$profil) {
                    throw new NotFoundHttpException('Profil introuvable');
                }
        
                // Marquer comme supprimé côté DTO pour que le mapper propage sur l'entité
                $dto->deletedAt = now()->setTimeZone(new \DateTimeZone('Europe/Paris'));
        
                $this->mapper->map($dto, $profil);
                $this->profilRepository->save($profil);
    }
}