<?php

declare(strict_types=1);

namespace App\Infrastructure\EarlyAccess\Form;

use App\Application\EarlyAccess\DTO\Request\CreateEarlyAccessRequest;
use App\Domain\EarlyAccess\ValueObject\UserSegment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EarlyAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'label' => 'Adresse email professionnelle',
                'attr' => [
                    'data-form-target' => 'input',
                    'placeholder' => 'votre.email@entreprise.com',
                    'autocomplete' => 'email',
                    'autofocus' => true,
                ],
            ])
            ->add('lastName', TextType::class, [
                'empty_data' => '',
                'label' => 'Nom complet',
                'attr' => [
                    'data-form-target' => 'input',
                    'autocomplete' => 'given-name',
                ],
            ])
            ->add('userSegment', EnumType::class, [
                'label' => 'Vous êtes ?',
                'class' => UserSegment::class,
                'choice_label' => fn (UserSegment $choice) => $choice->label(),
                'attr' => [
                    'data-form-target' => 'input',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
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
            'data_class' => CreateEarlyAccessRequest::class,
        ]);
    }
}
