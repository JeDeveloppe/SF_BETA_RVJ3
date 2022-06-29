<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('document', ChoiceType::class, [
                'label' => 'Type de document:',
                'choices' => [
                    'FA' => 'numeroFacture',
                    'DEVIS' => 'numeroDevis'
                ],
                'required' => true,
                'placeholder' => '...'
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro:',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Juste les chiffres...'
                ]
            ])
            ->add('Rechercher', SubmitType::class, [
                'attr' => ['class' => 'btn btn-lg btn-outline-success'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
