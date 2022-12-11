<?php

namespace App\Form\Admin;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VilleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeCodePostal', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('villeNom', TextType::class, [
                'label' => 'Nom de ville'
            ])
            ->add('villeDepartement', TextType::class, [
                'label' => 'Département ou province'
            ])
            ->add('lng', TextType::class, [
                'label' => 'Longitude'
            ])
            ->add('lat', TextType::class, [
                'label' => 'Latitude'
            ])
            ->add('pays', ChoiceType::class, [
                'label' => 'Pays',
                'placeholder' => "Choisir un pays...",
                'choices' => [
                    "FRANCE"   => "FR",
                    "BELGIQUE" => "BE"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
