<?php

namespace App\Form;

use App\Entity\MethodeEnvoi;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MethodeEnvoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => MethodeEnvoi::class,
                'label' => false,
                'choice_label' => 'name',
                'placeholder' => 'Choix d\'envoi ou retrait...',
                'required' => true
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
