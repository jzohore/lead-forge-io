<?php

declare(strict_types=1);

namespace App\Infrastructure\ReportBug\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/app/signaler-un-bug', name: 'app_report_bug_')]
class AppReportBugController extends AbstractController
{
    #[Route(
        path: '',
        name: 'request',
        methods: ['GET', 'POST'],
    )]
    public function reportBugRequest(): Response
    {
        return $this->render('application/report-bug/report_bug_request.html.twig');
    }
}
