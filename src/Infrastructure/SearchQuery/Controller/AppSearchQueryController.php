<?php

declare(strict_types=1);

namespace App\Infrastructure\SearchQuery\Controller;

use App\Infrastructure\Company\Service\SireneApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/app/nouvelle-recherche', name: 'app_search_query_')]
class AppSearchQueryController extends AbstractController
{
    #[Route(
        path: '',
        name: 'request',
        methods: ['GET', 'POST'],
    )]
    public function searchQueryRequest(SireneApiService $apiService): Response
    {
        dump($apiService->searchCompaniesByName('asso'));
        return $this->render('application/search_query/search_query_request.html.twig', [
            'page_title' => 'Nouvelle recherche de Leads'
        ]);
    }
}
