<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PartenaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom:'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:'
            ])
            ->add('collecte', TextareaType::class, [
                'label' => 'Détails de ce que le partenaire collect:'
            ])
            ->add('vend', TextareaType::class, [
                'label' => 'Détails de ce que le partenaire vend:'
            ])
            ->add('url', UrlType::class, [
                'label' => 'Adresse du site internet:'
            ])
            ->add('country', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'name',
                'label' => 'Pays:'
            ])
            ->add('imageBlob', FileType::class, [
                'label' => 'Image:',
                'data_class' => null,
                'required' => false,
                'mapped' => false
            ])
            ->add('isDon', CheckboxType::class, [
                'label' => 'Accepte les dons',
                'required' => false
            ])
            ->add('isDetachee', CheckboxType::class, [
                'label' => 'Vend des pièces détachées',
                'required' => false
            ])
            ->add('isComplet', CheckboxType::class, [
                'label' => 'Vend des jeux complet',
                'required' => false
            ])
            ->add('isEcommerce', CheckboxType::class, [
                'label' => 'Est un site e-commerce',
                'required' => false
            ])
            ->add('isOnLine', CheckboxType::class, [
                'label' => 'En ligne sur Refaitesvosjeux.fr',
                'label_attr' => [
                    'class' => 'switch-custom'
                    ],
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
