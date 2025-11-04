<?php

declare(strict_types=1);

namespace App\Application\ReportBug\UseCase;

use App\Application\ReportBug\DTO\ReportBugDTO;
use App\Domain\ReportBug\Port\Out\ReportBugRepositoryInterface;
use App\Domain\ReportBug\Entity\ReportBug;
use App\Domain\ReportBug\Port\In\UpdateReportBugInterface;
use Symfony\Component\Uid\Uuid;
use function Symfony\Component\Clock\now;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


final readonly class UpdateReportBugUseCase implements UpdateReportBugInterface
{
    public function __construct(
        private ReportBugRepositoryInterface $reportBugRepository,
        private ObjectMapperInterface $mapper,
    ) {}

    public function __invoke(ReportBugDTO $dto)
    {
                /** @var Uuid|null $id */
                $id = $dto->uuid;
                if (!$id) {
                    throw new NotFoundHttpException('ReportBug introuvable');
                }
        
                $reportBug = $this->reportBugRepository->getById($id);
                if (!$reportBug) {
                    throw new NotFoundHttpException('ReportBug introuvable');
                }
        
                $this->mapper->map($dto, $reportBug);
                $this->reportBugRepository->save($reportBug);
        
                return $reportBug;
    }
}