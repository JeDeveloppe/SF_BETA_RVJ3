<?php

namespace App\Form;

use App\Entity\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DocumentPaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'label' => 'Saisir un paiement manuellement:',
                'required' => true,
                'widget' => "single_text",
                'html5' => false,
                'format' => 'dd/MM/yyyy'
            ])
            ->add('moyenPaiement', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'CB' => 'CB',
                    'VIREMENT' => 'VIREMENT',
                    'ESPECE' => 'ESPECE',
                    'CHEQUE' => 'CHEQUE'
                ],
                'required' => true,
                'placeholder' => 'Saisir un paiement...'
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-lg btn-outline-success'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //
        ]);
    }
}
