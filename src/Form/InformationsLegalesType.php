<?php

namespace App\Form;

use App\Entity\InformationsLegales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformationsLegalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSociete')
            ->add('adresseSociete')
            ->add('siretSociete')
            ->add('adresseMailSite')
            ->add('siteUrl')
            ->add('societeWebmaster')
            ->add('nomWebmaster')
            ->add('hebergeurSite')
            ->add('tauxTva')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformationsLegales::class,
        ]);
    }
}
