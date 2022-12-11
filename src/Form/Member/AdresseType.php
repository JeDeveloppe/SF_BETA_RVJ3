<?php

namespace App\Form\Member;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\Member\AdressesVilleAutocompleteField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $this->department = $options['department'];

        $builder
            ->add('organisation', TextType::class, [
                'label' => 'Organisation:',
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom:'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom:'
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse:'
            ])
            ->add('ville', AdressesVilleAutocompleteField::class)
            // ->add('ville', EntityType::class, [
            //     'class' => Ville::class,
            //     'label' => "Code postal et ville:",
            //     'placeholder' => "Choisissez une ville dans la liste...",
            //     'choice_label' => function (Ville $ville) {
            //         return $ville->getVilleCodePostal() . ' ' . $ville->getVilleNom();
            //     },
            //     'query_builder' => function (EntityRepository $er) {
            //         return $er->createQueryBuilder('v')
            //             ->where('v.villeDepartement = '.$this->department)
            //             ->orderBy('v.villeCodePostal', 'ASC');
            //     }
            // ])
            ->add('isFacturation', HiddenType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
            'department' => null,
        ]);
    }
}
