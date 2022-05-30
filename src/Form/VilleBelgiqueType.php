<?php

namespace App\Form;

use App\Entity\VilleBelgique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleBelgiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeCodePostal')
            ->add('villeNom')
            ->add('lng')
            ->add('lat')
            ->add('villeProvince')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VilleBelgique::class,
        ]);
    }
}
