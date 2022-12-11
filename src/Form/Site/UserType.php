<?php

namespace App\Form\Site;

use App\Entity\Pays;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse email:',
                'attr' => [
                    'readonly' => true
                ]
            ])
            // ->add('roles')
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe:',
                'attr' => [
                    'placeholder' => '* * * * * * * * * *',
                    'readonly' => true
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone:'
            ])
            ->add('country', EntityType::class, [
                'label' => 'Pays:',
                'choice_label' => 'name',
                'class' => Pays::class
            ])            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
