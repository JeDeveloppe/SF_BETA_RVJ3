<?php

namespace App\Form\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CatalogueFiltersType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filters', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    "Affichage par date d'ajouts"  => "ajout",
                    "Affichage par nom"     => "nom",
                    "Affichage par éditeur" => "editeur",
                    "Affichage par année"   => "annee"
                ],
                'data' => $options['tri']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'tri' => []
        ]);
    }
}
