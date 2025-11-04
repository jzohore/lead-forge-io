<?php

declare(strict_types=1);

namespace App\Infrastructure\SearchQuery\Form;

use App\Application\SearchQuery\DTO\Request\SearchQueryCreateRequest;
use App\Domain\SearchQuery\ValueObject\CompanySizeLevel;
use App\Domain\SearchQuery\ValueObject\Industry;
use App\Domain\SearchQuery\ValueObject\LeadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', TextType::class, [
                'label' => 'Mot-clé ou rôle du lead',
                'required' => true,
                'attr' => [
                    'data-form-target' => 'input',
                    'placeholder' => 'Ex: CTO SaaS, CEO Fintech, Stripe',
                ],
            ])
            ->add('leadType', EnumType::class, [
                'label' => 'Type de lead',
                'class' => LeadType::class,
                'choice_label' => fn (LeadType $choice) => $choice->label(),
                'attr' => [
                    'data-form-target' => 'input',
                ],
            ])
//            ->add('industry', EnumType::class, [
//                'label' => 'Secteur (optionnel)',
//                'class' => Industry::class,
//                'choice_label' => fn (Industry $choice) => $choice->label(),
//                'attr' => [
//                    'data-form-target' => 'input',
//                ],
//            ])
//            ->add('maxResults', IntegerType::class, [
//                'label' => 'Nombre maximum de résultats',
//                'required' => false,
//                'empty_data' => 25,
//            ])
//            ->add('sizeLevel', EnumType::class, [
//                'label' => 'Secteur (optionnel)',
//                'class' => CompanySizeLevel::class,
//                'choice_label' => fn (CompanySizeLevel $choice) => $choice->label(),
//                'attr' => [
//                    'data-form-target' => 'input',
//                ],
//            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Lancer la recherche',
                'attr' => [
                    'class' => 'btn btn-sm text-white bg-[#6e2f1e]',
                    'data-form-target' => 'submit',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchQueryCreateRequest::class,
        ]);
    }
}
