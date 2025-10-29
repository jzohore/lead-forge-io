<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'website_homepage', methods: ['GET'])]
    public function websiteHomePage(): Response
    {
        return $this->render('website/homepage.html.twig');
    }
}
