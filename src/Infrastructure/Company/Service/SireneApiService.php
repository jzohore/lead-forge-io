<?php

declare(strict_types=1);

namespace App\Infrastructure\Company\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SireneApiService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiUrl = 'https://recherche-entreprises.api.gouv.fr/search'
    ) {}

    /**
     * Recherche d'entreprises par nom via l'API publique
     */
    public function searchCompaniesByName(string $name, int $page = 1, int $perPage = 10): array
    {
        $response = $this->client->request('GET', $this->apiUrl, [
            'query' => [
                'q' => $name,
                'page' => $page,
                'per_page' => $perPage,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $data = $response->toArray();

        return $data['results'] ?? [];
    }

    /**
     * Récupère les infos détaillées pour une entreprise via SIREN/SIRET.
     */
    public function getCompanyInfo(string $siren): array
    {
        $response = $this->client->request('GET', $this->apiUrl.'/'.$siren, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiToken,
            ],
        ]);

        $data = $response->toArray();

        // Retourne un tableau minimal pour enrichissement Company
        return [
            'dirigeantNom' => $data['dirigeant'] ?? null,
            'dirigeantPrenom' => $data['prenom'] ?? null,
            'ca' => $data['chiffre_affaires'] ?? null,
            'sectionActivitePrincipale' => $data['section_activite_principale'] ?? null,
            'website' => $data['site_web'] ?? null,
            'siren' => $data['siren'] ?? null,
            'name' => $data['nom_entreprise'] ?? null,
            'industry' => $data['activite_principale'] ?? null,
            'sizeLevel' => $data['categorie_entreprise'] ?? null,
            'city' => $data['ville'] ?? null,
            'postalCode' => $data['code_postal'] ?? null,
            'region' => $data['region'] ?? null,
        ];
    }

    /**
     * Transforme le tableau API en entité Company.
     */
    public function mapToCompanyEntity(array $data): Company
    {
        $company = new Company();

        $company->setName($data['name'] ?? '');
        $company->setSiren($data['siren'] ?? null);
        $company->setDirigeantNom($data['dirigeantNom'] ?? null);
        $company->setDirigeantPrenom($data['dirigeantPrenom'] ?? null);
        $company->setCa($data['ca'] ?? null);
        $company->setSectionActivitePrincipale($data['sectionActivitePrincipale'] ?? null);
        $company->setWebsite($data['website'] ?? null);
        $company->setCity($data['city'] ?? null);
        $company->setPostalCode($data['postalCode'] ?? null);
        $company->setRegion($data['region'] ?? null);

        if (isset($data['industry'])) {
            $company->setIndustry(Industry::from($data['industry']));
        }

        if (isset($data['sizeLevel'])) {
            $company->setSizeLevel(CompanySizeLevel::from($data['sizeLevel']));
        }

        return $company;
    }
}
