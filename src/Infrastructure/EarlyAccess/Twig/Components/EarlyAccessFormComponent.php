<?php

declare(strict_types=1);

namespace App\Infrastructure\EarlyAccess\Twig\Components;

use App\Application\EarlyAccess\DTO\Request\CreateEarlyAccessRequest;
use App\Domain\EarlyAccess\Port\In\CreateEarlyAccessInterface;
use App\Infrastructure\EarlyAccess\Form\EarlyAccessType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'EarlyAccessFormComponent',
    template: 'components/EarlyAccess/EarlyAccessFormComponent.html.twig',
)]
class EarlyAccessFormComponent
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public bool $isSuccessful = false;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CreateEarlyAccessInterface $createEarlyAccess,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(EarlyAccessType::class, new CreateEarlyAccessRequest());
    }

    #[LiveAction]
    public function save()
    {
        $this->submitForm();

        try {
            $dto = $this->form->getData();

            ($this->createEarlyAccess)($dto);

            $this->isSuccessful = true;
        } catch (\DomainException $e) {
            // ✅ Flash error au lieu de success
            $this->requestStack->getSession()->getFlashBag()->add('error', $e->getMessage());

            return null;
        }

    }
}
