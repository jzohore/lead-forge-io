<?php

declare(strict_types=1);

namespace App\Infrastructure\SearchQuery\Twig\Components;

use App\Application\SearchQuery\DTO\Request\SearchQueryCreateRequest;
use App\Domain\SearchQuery\Port\In\CreateSearchQueryInterface;
use App\Infrastructure\SearchQuery\Form\SearchQueryType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'SearchQueryFormComponent',
    template: 'components/SearchQuery/SearchQueryFormComponent.html.twig',
)]
class SearchQueryFormComponent
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CreateSearchQueryInterface $createSearchQuery,
        private readonly ValidatorInterface $validator,
    ) {
    }
    #[LiveProp]
    public ?Uuid $userId = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(SearchQueryType::class, new SearchQueryCreateRequest());
    }

    #[LiveAction]
    public function save()
    {
        $this->submitForm();
        $query = null;

        try {
            $dto = $this->getForm()->getData();
            $dto->userId = $this->userId;
            $query = ($this->createSearchQuery)($dto);
            //dd($query);
        } catch (\DomainException $e) {
            // ✅ Flash error au lieu de success
            $this->requestStack->getSession()->getFlashBag()->add('error', $e->getMessage());

            return null;
        }

        return new RedirectResponse($this->urlGenerator->generate('app_search_query_request'));
    }
}
