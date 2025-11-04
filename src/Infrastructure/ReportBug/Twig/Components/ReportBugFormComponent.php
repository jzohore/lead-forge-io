<?php

declare(strict_types=1);

namespace App\Infrastructure\ReportBug\Twig\Components;

use App\Application\ReportBug\DTO\Request\CreateReportBugRequest;
use App\Application\UploadFile\Request\FileUploaderRequest;
use App\Domain\ReportBug\Port\In\CreateReportBugInterface;
use App\Infrastructure\ReportBug\Form\ReportBugType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'ReportBugFormComponent',
    template: 'components/ReportBug/ReportBugFormComponent.html.twig',
)]
class ReportBugFormComponent
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?Uuid $userId = null;

    #[LiveProp]
    public ?string $singleFileUploadError = null;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CreateReportBugInterface $createReportBug,
        private readonly ValidatorInterface $validator,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(ReportBugType::class, new CreateReportBugRequest());
    }

    private function validateSingleFile(UploadedFile $singleFileUpload): void
    {
        $errors = $this->validator->validate($singleFileUpload, [
            new Assert\File([
                'maxSize' => '1M',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ],
                'mimeTypesMessage' => 'Veuillez téléverser une image au format JPEG, PNG ou WebP.',
                'maxSizeMessage' => 'L’image ne doit pas dépasser 1 Mo.',
            ]),
        ]);

        if (0 === \count($errors)) {
            return;
        }

        $this->singleFileUploadError = $errors->get(0)->getMessage();

        // causes the component to re-render
        throw new UnprocessableEntityHttpException('Validation failed');
    }

    #[LiveAction]
    public function save(Request $request, FileUploaderRequest $fileUploaderRequest)
    {
        $this->submitForm();

        try {
            $dto = $this->form->getData();
            $singleFileUpload = $request->files->get('single');
            if ($singleFileUpload) {
                $this->validateSingleFile($singleFileUpload);
            }

            if ($singleFileUpload) {
                $filename = $fileUploaderRequest->upload($singleFileUpload);
                $dto->filename = $filename->filename;
                $dto->mimeType = $filename->mimeType;
            } else {
                $this->singleFileUploadError = 'Aucun fichier envoyé.';
            }

            $dto->userId = $this->userId;

            $reportBug = ($this->createReportBug)($dto);
            $this->requestStack->getSession()->getFlashBag()->add('success', $reportBug->messageSuccess);
        } catch (\DomainException $e) {
            // ✅ Flash error au lieu de success
            $this->requestStack->getSession()->getFlashBag()->add('error', $reportBug->message);

            return null;
        }

        return new RedirectResponse($this->urlGenerator->generate('app_report_bug_request'));
    }
}
