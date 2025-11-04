<?php

declare(strict_types=1);

namespace App\Infrastructure\Company\Form;

use App\Application\Company\DTO\CompanyDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // TODO: ajouter les champs mappés au DTO (ex: ->add('title'))
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyDTO::class,
        ]);
    }
}