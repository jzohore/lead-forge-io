<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Controller\Website;

use App\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    /**
     * @throws \DateMalformedStringException
     */
    #[Route(path: '', name: 'application_redirect_to_home', methods: ['GET'])]
    public function checkUserRedirectToHome(#[CurrentUser] ?User $user): Response
    {
        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)
                || in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)) {
                $this->addFlash('success', '👋 Bienvenue '.$user->firstName);

                return $this->redirectToRoute('ad_dashboard');

            } else {
                $this->addFlash('success', '👋 Bienvenue '.$user->firstName);

                return $this->redirectToRoute('app_user_account_informations', [
                    '_locale' => $user->lang,
                ]);
            }
        }

        return $this->redirectToRoute('website_homepage');
    }

    #[Route(path: '/{_locale<%app.supported_locales%>}/', name: 'website_homepage', methods: ['GET'])]
    public function websiteHomePage(): Response
    {
        return $this->render('website/homepage.html.twig');
    }
}
