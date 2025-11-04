<?php

declare(strict_types=1);

namespace App\Application\Company\UseCase;

use App\Application\Company\DTO\CompanyDTO;
use App\Domain\Company\Port\Out\CompanyRepositoryInterface;
use App\Domain\Company\Entity\Company;
use App\Domain\Company\Port\In\DeleteCompanyInterface;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


final readonly class DeleteCompanyUseCase implements DeleteCompanyInterface
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private ObjectMapperInterface $mapper,
    ) {}

    public function __invoke(CompanyDTO $dto)
    {
                /** @var Uuid|null $id */
                $id = $dto->uuid;
                if (!$id) {
                    throw new NotFoundHttpException('Company introuvable');
                }
        
                $company = $this->companyRepository->getById($id);
                if (!$company) {
                    throw new NotFoundHttpException('Company introuvable');
                }
        
                // Marquer comme supprimé côté DTO pour que le mapper propage sur l'entité
                $dto->deletedAt = now()->setTimeZone(new \DateTimeZone('Europe/Paris'));
        
                $this->mapper->map($dto, $company);
                $this->companyRepository->save($company);
    }
}