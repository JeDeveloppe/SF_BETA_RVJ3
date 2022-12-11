<?php

namespace App\Form\Admin;

use App\Entity\Occasion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OccasionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('reference')
            ->add('priceHt', NumberType::class, [
                'label' => "Prix de vente HT (en cents)"
            ])
            // ->add('oldPriceHt')
            ->add('information')
            ->add('isNeuf')
            ->add('etatBoite', ChoiceType::class, [
                'placeholder' => 'Choisir dans la liste...',
                'choices' => [
                    'COMME NEUF' => 'COMME NEUF', 
                    'BON ÉTAT' => 'BON ÉTAT', 
                    'ÉTAT MOYEN' => 'ÉTAT MOYEN', 
                ]
            ])
            ->add('etatMateriel', ChoiceType::class, [
                'placeholder' => 'Choisir dans la liste...',
                'choices' => [
                    'COMME NEUF' => 'COMME NEUF', 
                    'BON ÉTAT' => 'BON ÉTAT', 
                    'ÉTAT MOYEN' => 'ÉTAT MOYEN',
                ]
            ])
            ->add('regleJeu', ChoiceType::class, [
                'placeholder' => 'Choisir dans la liste...',
                'choices' => [
                    'ORIGINALE'    => 'ORIGINALE', 
                    'IMPRIMEE'     => 'IMPRIMEE', 
                    'SANS'         => 'SANS',
                    'SUR LA BOITE' => 'SUR LA BOITE'
                ]
            ])
            ->add('isOnLine')
            // ->add('boite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Occasion::class,
        ]);
    }
}
