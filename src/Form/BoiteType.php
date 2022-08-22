<?php

namespace App\Form;

use App\Entity\Boite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BoiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageblob', FileType::class, [
                'data_class' => null,
                'required' => false
            ])
            ->add('nom')
            ->add('editeur')
            ->add('annee')
            ->add('slug', TextType::class, [
                'required' => false
            ])
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
