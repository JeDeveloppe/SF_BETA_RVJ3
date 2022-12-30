<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminSearchVilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une ville ou un code postal...'
                ]
            ])
            ->add('rechercher', SubmitType::class, [
                'attr' => ['class' => 'btn btn-sm btn-outline-success'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
