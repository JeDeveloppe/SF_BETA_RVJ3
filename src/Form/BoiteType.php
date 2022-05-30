<?php

namespace App\Form;

use App\Entity\Boite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('editeur')
            ->add('annee')
            ->add('imageblob')
            ->add('slug')
            ->add('poidBoite')
            ->add('age')
            ->add('nbrJoueurs')
            ->add('prixHt')
            ->add('contenu')
            ->add('message')
            ->add('isOnLine')
            ->add('isLivrable')
            ->add('isComplet')
            ->add('isDeee')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boite::class,
        ]);
    }
}
