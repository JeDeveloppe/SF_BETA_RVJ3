<?php

namespace App\Form;

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
                'label' => 'Prix de l\' adhésion (en cents HT):'
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
