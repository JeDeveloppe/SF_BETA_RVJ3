<?php

namespace App\Form\Admin;

use App\Entity\MethodeEnvoi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MethodeEnvoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'label' => false,
                'choice_label' => 'name',
                'placeholder' => 'Choix d\'envoi ou retrait...',
                'required' => true,
                'choices' => [
                    'Envoi par la poste' => 'poste'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MethodeEnvoi::class,
        ]);
    }
}
