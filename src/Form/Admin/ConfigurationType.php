<?php

namespace App\Form\Admin;

use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('versionSite', TextType::class, [
                'label' => 'Version actuelle du site:',
                'required' => false,
            ])
            ->add('DevisDelayBeforeDelete', NumberType::class, [
                'label' => 'Nombre de jours avant relance d\'un devis (x2 avant suppression):'
            ])
            ->add('prefixeFacture', TextType::class, [
                'label' => 'Préfixe des factures:'
            ])
            ->add('prefixeDevis', TextType::class, [
                'label' => 'Préfixe des devis:'
            ])
            ->add('cost', NumberType::class, [
                'label' => 'Prix de l\' adhésion (TTC):'
            ])
            ->add('grandPlateauBois', TextType::class, [
                'label' => 'Prix du grand plateau / support en bois (TTC):'
            ])
            ->add('grandPlateauPlastique', TextType::class, [
                'label' => 'Prix du grand plateau / support en plastique (TTC):'
            ])
            ->add('petitPlateauBois', TextType::class, [
                'label' => 'Prix du petit plateau / support en bois (TTC):'
            ])
            ->add('petitPlateauPlastique', TextType::class, [
                'label' => 'Prix du petit plateau / support en plastique (TTC):'
            ])
            ->add('pieceUnique', TextType::class, [
                'label' => 'Prix de la pièce unique dans un jeu:'
            ])
            ->add('pieceMultiple', TextType::class, [
                'label' => 'Prix de la pièce en grande quantité:'
            ])
            ->add('pieceMetalBois', TextType::class, [
                'label' => 'Prix de la pièce en métal / bois:'
            ])
            ->add('autrePiece', TextType::class, [
                'label' => 'Prix des autres pièces:'
            ])
            ->add('enveloppeSimple', NumberType::class, [
                'label' => 'Prix de l\' enveloppe simple (TTC):'
            ])
            ->add('enveloppeBulle', NumberType::class, [
                'label' => 'Prix de l\' enveloppe à bulles (TTC):'
            ])
            ->add('holiday', TextType::class, [
                'label' => 'Texte vacances:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Laisser vide hors vacances...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
        ]);
    }
}
