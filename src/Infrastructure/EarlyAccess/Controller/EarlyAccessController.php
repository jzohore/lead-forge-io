<?php

declare(strict_types=1);

namespace App\Infrastructure\EarlyAccess\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EarlyAccessController extends AbstractController
{
    #[Route(
        path: '/essayez-gratuitement',
        name: 'website_early_access',
        methods: ['GET'],
    )]
    public function index(): Response
    {
        return $this->render('website/early_access.html.twig');
    }
}
