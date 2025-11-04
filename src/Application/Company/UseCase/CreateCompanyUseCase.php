<?php

declare(strict_types=1);

namespace App\Application\Company\UseCase;

use App\Application\Company\DTO\Request\CompanyCreateRequest;
use App\Application\Company\DTO\Response\CompanyCreatedResponse;
use App\Application\Company\DTO\Response\CompanyErrorResponse;
use App\Domain\Company\Entity\Company;
use App\Domain\Company\Port\In\CreateCompanyInterface;
use App\Domain\Company\Port\Out\CompanyRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class CreateCompanyUseCase implements CreateCompanyInterface
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private ObjectMapperInterface $mapper,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CompanyCreateRequest $request): CompanyCreatedResponse|CompanyErrorResponse
    {
        try {
            $company = $this->companyRepository->getBySiren($request->siren);
            if ($company) {
                $this->logger->info('Company already exists', [
                    'company' => $company,
                ]);
                return CompanyErrorResponse::sirenAlreadyExists(
                    siren: $request->siren,
                    companyName: $request->name,
                );
            }

            $companyRequest = new Company();

            $this->mapper->map($request, $companyRequest);

            $this->companyRepository->save($companyRequest);

            $this->logger->info('Requête créer avec succès', [
                'company_id' => $companyRequest->id->toRfc4122(),
                'Nom' => $companyRequest->name,
            ]);

            return CompanyCreatedResponse::fromEntity($companyRequest);
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de la création d\'une entreprise', [
                'query' => $request->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return CompanyErrorResponse::validationFailed([
                'system' => 'Une erreur est survenue lors de la création d\'une entreprise ',
            ]);
        }
    }
}
