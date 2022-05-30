<?php

namespace App\Form;

use App\Entity\VilleFrance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleFranceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeCodePostal')
            ->add('villeNom')
            ->add('villeDepartement')
            ->add('lng')
            ->add('lat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VilleFrance::class,
        ]);
    }
}
