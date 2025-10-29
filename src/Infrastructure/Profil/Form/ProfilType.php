<?php

declare(strict_types=1);

namespace App\Infrastructure\Profil\Form;

use App\Application\Profil\DTO\ProfilDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // TODO: ajouter les champs mappés au DTO (ex: ->add('title'))
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilDTO::class,
        ]);
    }
}