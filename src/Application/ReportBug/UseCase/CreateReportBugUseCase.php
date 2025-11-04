<?php

declare(strict_types=1);

namespace App\Application\ReportBug\UseCase;

use App\Application\ReportBug\DTO\Request\CreateReportBugRequest;
use App\Application\ReportBug\DTO\Response\ReportBugCreatedResponse;
use App\Application\ReportBug\DTO\Response\ReportBugErrorResponse;
use App\Application\User\DTO\Response\UserErrorResponse;
use App\Domain\ReportBug\Entity\ReportBug;
use App\Domain\ReportBug\Port\In\CreateReportBugInterface;
use App\Domain\ReportBug\Port\Out\ReportBugRepositoryInterface;
use App\Domain\User\Port\Out\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class CreateReportBugUseCase implements CreateReportBugInterface
{
    public function __construct(
        private ReportBugRepositoryInterface $reportBugRepository,
        private ObjectMapperInterface $mapper,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateReportBugRequest $request): ReportBugCreatedResponse
    |UserErrorResponse|ReportBugErrorResponse
    {
        try {
            $user = $this->userRepository->getById($request->userId);
            $email = $user->email;
            if (! $email) {
                $this->logger->warning('Email introuvable', [
                    'email' => $email,
                ]);

                return UserErrorResponse::userNotFound($email);
            }
            $reportBug = new ReportBug();
            $reportBug->owner = $user;
            $this->mapper->map($request, $reportBug);

            $this->reportBugRepository->save($reportBug);
            $this->logger->info('Bug signalé avec succès', [
                'userId' => $reportBug->id->toRfc4122(),
                'email' => $email,
                'message' => $reportBug->message,
            ]);

            return ReportBugCreatedResponse::fromEntity($reportBug);
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de la création d\'un bug', [
                'message' => $request->message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ReportBugErrorResponse::validationFailed([
                'system' => 'Une erreur est survenue lors de la création d\'un bug',
            ]);
        }
    }
}
