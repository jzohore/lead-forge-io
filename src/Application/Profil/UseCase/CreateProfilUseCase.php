<?php

declare(strict_types=1);

namespace App\Application\Profil\UseCase;

use App\Application\Profil\DTO\ProfilDTO;
use App\Domain\Profil\Port\Out\ProfilRepositoryInterface;
use App\Domain\Profil\Entity\Profil;
use App\Domain\Profil\Port\In\CreateProfilInterface;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


final readonly class CreateProfilUseCase implements CreateProfilInterface
{
    public function __construct(
        private ProfilRepositoryInterface $profilRepository,
        private ObjectMapperInterface $mapper,
    ) {}

    public function __invoke(ProfilDTO $dto)
    {
                $profil = new Profil();
                $this->mapper->map($dto, $profil);
                $this->profilRepository->save($profil);
        
                return $profil;
    }
}