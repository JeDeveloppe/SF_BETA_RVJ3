<?php

namespace App\Form\Site;


use App\Entity\Boite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchOccasionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom du jeu ou éditeur...'
                ]
            ])
            ->add('age', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Tous les âges...' => '0',
                    'A partir de 1 an' => '1',
                    'A partir de 2 ans' => '2',
                    'A partir de 3 ans' => '3',
                    'A partir de 4 ans' => '4',
                    'A partir de 5 ans' => '5',
                    'A partir de 6 ans' => '6',
                    'A partir de 7 ans' => '7',
                    'A partir de 8 ans' => '8',
                    'A partir de 9 ans' => '9',
                    'A partir de 10 ans' => '10',
                    'A partir de 12 ans' => '12',
                    'A partir de 14 ans' => '14',
                    'A partir de 16 ans' => '16',
                ]
            ])
            ->add('nbrJoueurs', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Tous joueurs...' => '0',
                    '2 joueurs et plus' => '2',
                    '3 joueurs et plus' => '3',
                    '4 joueurs et plus' => '4',
                    '5 joueurs et plus' => '5',
                    '6 joueurs et plus' => '6',
                    '7 joueurs et plus' => '7',
                    '8 joueurs et plus' => '8',
                    'Uniquement 1 joueur' => 'u1',
                    'Uniquement 2 joueurs' => 'u2',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boite::class,
        ]);
    }
}
