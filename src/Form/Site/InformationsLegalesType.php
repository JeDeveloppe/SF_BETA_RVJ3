<?php

namespace App\Form\Site;

use App\Entity\Pays;
use App\Entity\InformationsLegales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('country', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformationsLegales::class,
        ]);
    }
}
