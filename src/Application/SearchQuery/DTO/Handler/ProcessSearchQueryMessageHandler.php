<?php

declare(strict_types=1);

namespace App\Application\SearchQuery\DTO\Handler;

use App\Application\Company\DTO\Request\CompanyCreateRequest;
use App\Application\SearchQuery\DTO\Message\ProcessSearchQueryMessage;
use App\Domain\Company\Port\In\CreateCompanyInterface;
use App\Domain\Company\Port\Out\CompanyRepositoryInterface;
use App\Domain\SearchQuery\Entity\SearchQuery;
use App\Domain\SearchQuery\Port\Out\SearchQueryRepositoryInterface;
use App\Domain\SearchQuery\ValueObject\LeadType;
use App\Infrastructure\Company\Service\SireneApiService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final readonly class ProcessSearchQueryMessageHandler
{
    public function __construct(
        private SearchQueryRepositoryInterface $repository,
        private LoggerInterface $logger,
        private CompanyRepositoryInterface $companyRepository,
        private SireneApiService $apiService,
        private CreateCompanyInterface $createCompany,

        // injectez d'autres services métier ici (API scraping, enrichment, email verification, etc.)
    ) {
    }

    public function __invoke(ProcessSearchQueryMessage $message): void
    {
        $id = Uuid::fromString($message->searchQueryId);
        $searchQuery = $this->repository->getById($id);

        if (! $searchQuery) {
            $this->logger->error('SearchQuery not found', ['id' => $message->searchQueryId]);

            return;
        }

        try {
            // 1️⃣ Passage au statut PROCESSING
            $searchQuery->markInProcess();
            $this->repository->save($searchQuery);

            $this->processCompany($searchQuery);

            // 3️⃣ Exemple de collecte emails fictive
            if (LeadType::EMAIL === $searchQuery->leadType) {
                $foundEmails = $this->processSearchQuery($searchQuery);
                foreach ($foundEmails as $email) {
                    $searchQuery->incrementResults();
                }
            }


        } catch (\Throwable $e) {
            // Gestion des erreurs → statut FAILED

            $searchQuery->markFailed($e->getMessage());
            $this->repository->save($searchQuery);

            $this->logger->error('Error processing SearchQuery', [
                'id' => $searchQuery->id,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Méthode fictive représentant le traitement métier.
     *
     * @return array<string> emails trouvés
     */
    private function processSearchQuery($searchQuery): array
    {
        // Ici tu peux appeler des services API, scraping, verification emails, enrichissement
        // Exemple mock
        return [
            'test1@example.com',
            'test2@example.com',
        ];
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function processCompany(SearchQuery $searchQuery): void
    {
        // 2️⃣ Traitement pour les entreprises
        if (LeadType::COMPANY === $searchQuery->leadType) {
            // a) Chercher les entreprises existantes en base
            $companies = $this->companyRepository->getByName($searchQuery->query);

            // b) Appel API Sirene si aucune entreprise trouvée
            if (empty($companies)) {
                $apiCompanies = $this->apiService->searchCompaniesByName($searchQuery->query);

                foreach ($apiCompanies as $index => $data) {
                    try {
                        if (! isset($data['siren'])) {
                            continue;
                        }

                        $name = $data['nom_raison_sociale'] ?? $data['nom_complet'] ?? null;
                        if (empty($name)) {
                            continue;
                        }


                        $dto = new CompanyCreateRequest();
                        $dto->name = $name;
                        $dto->siren = $data['siren'];
                        $dto->siret = $data['siege']['siret'];
                        $dto->geoAdresse = $data['siege']['geo_adresse'] ?? null;
                        $dto->sectionActivitePrincipale = $data['section_activite_principale'] ?? null;

                        // dateCreation sécurisée
                        $dto->dateCreation = null;
                        if (! empty($data['date_creation'])) {
                            try {
                                $dto->dateCreation = new \DateTimeImmutable($data['date_creation']);
                            } catch (\Exception $e) {
                                $this->logger->warning('Invalid date_creation', [
                                    'siren' => $data['siren'],
                                    'date_creation' => $data['date_creation'],
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }

                        $company = ($this->createCompany)($dto);
                        $companies[] = $company;
                    } catch (\Throwable $e) {
                        $this->logger->error('Error processing single API company', [
                            'data' => $data,
                            'error' => $e->getMessage(),
                        ]);

                        continue;
                    }
                }
            }

            // c) Mettre à jour le compteur de résultats dans SearchQuery
            $searchQuery->resultsCount = count($companies);
            $searchQuery->markCompleted();
            $this->repository->save($searchQuery);
        }
    }

    private function findCompaniesForQuery(SearchQuery $searchQuery): array
    {
        // Exemple : on cherche déjà les entreprises en base matching industry / sizeLevel
        return $this->companyRepository->findByFilters(
            industry: $searchQuery->industry,
            sizeLevel: $searchQuery->sizeLevel,
            //            region: $searchQuery->region,
            //            city: $searchQuery->city
        );
    }

    private function enrichCompanies(array $companies, SearchQuery $searchQuery): void
    {
        foreach ($companies as $company) {
            if (! $company->getWebsite() || ! $company->getDirigeantNom()) {
                // Appel à l’API Sirene ou autre service d’enrichissement
                $data = $this->apiService->getCompanyInfo($company->getSiren());

                $company->setDirigeantNom($data['dirigeantNom'] ?? null);
                $company->setDirigeantPrenom($data['dirigeantPrenom'] ?? null);
                $company->setCa($data['ca'] ?? null);
                $company->setSectionActivitePrincipale($data['sectionActivitePrincipale'] ?? null);
                $company->setWebsite($data['website'] ?? null);

                $this->companyRepository->save($company);
            }
        }
    }
}
