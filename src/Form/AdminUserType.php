<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email:'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'ADMIN' => 'ROLE_ADMIN',
                    'SUPER ADMIN' => 'ROLE_SUPER_ADMIN'
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles'
            ])
            ->add('level')
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe:',
                'attr' => [
                    'placeholder' => '********',
                    'readonly' => true
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone:'
            ])
            // ->add('department')
            // ->add('createdAt')
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo:'
            ])
            // ->add('country', EntityType::class, [
            //     'class' => Pays::class,
            //     'choice_label' => 'name'
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
