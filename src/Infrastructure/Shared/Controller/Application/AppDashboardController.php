<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppDashboardController extends AbstractController
{
    #[Route(
        path: '/app/tableau-de-bord',
        name: 'app_dashboard',
        methods: ['GET']
    )]
    public function appDashboard(): Response
    {
        return $this->render('application/dashboard.html.twig');
    }
}

