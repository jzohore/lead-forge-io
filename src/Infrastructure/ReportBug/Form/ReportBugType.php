<?php

declare(strict_types=1);

namespace App\Infrastructure\ReportBug\Form;

use App\Application\ReportBug\DTO\ReportBugDTO;
use App\Application\ReportBug\DTO\Request\CreateReportBugRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ReportBugType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'Que s\'est-il passé ?',
                'attr' => [
                    'data-form-target' => 'input',
                    'style' => 'height:180px; padding: 1rem;',
                    'placeholder' => 'Donnez-nous des précisions sur le problème',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
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
            'data_class' => CreateReportBugRequest::class,
        ]);
    }
}
