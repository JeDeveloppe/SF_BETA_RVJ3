<?php

namespace App\Form\Admin;

use App\Entity\Boite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BoiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageblob', FileType::class, [
                'data_class' => null,
                'required' => false,
                'label' => "Choisir une image:",
                'mapped' =>false
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de la boite:'
            ])
            ->add('editeur')
            ->add('annee', TextType::class, [
                'label' => 'Année:',
                'attr' => [
                    'placeholder' => '1983'
                ]
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'Slug (nom-de-la-boite):',
                'attr' => [
                    'placeholder' => 'nom-de-la-boite'
                ],
                'required' => false
            ])
            ->add('poidBoite', TextType::class, [
                'label' => 'Poid de la boite (g):',
                'required' => false,
                'attr' => [
                    'placeholder' => '400'
                ]
            ])
            ->add('age', ChoiceType::class, [
                'label' => 'Age',
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
                ],
                'required' => true,
            ])
            ->add('nbrJoueurs', ChoiceType::class, [
                'label' => 'Nombre de joueurs:',
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
                    'Uniquement 2 joueurs' => 'u2'
                ],
                'required' => true,
            ])
            ->add('prixHt', TextType::class, [
                'label' => 'Prix de vente en état neuf (cents HT):',
                'attr' => [
                    'placeholder' => '1050 pour 10.50'
                ]
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu de la boite:',
                'attr' => [
                    'placeholder' => 'Liste des articles de la boite ex: 1 plateau en bois'
                ]
            ])
            ->add('message', TextType::class, [
                'label' => 'Message spécial:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Message s\'il manque une pièce ou autre...'
                ]
            ])
            ->add('isOnLine', CheckboxType::class, [
                'label' => 'En ligne',
                'required' => false
            ])
            ->add('isLivrable', CheckboxType::class, [
                'label' => 'Disponible en livraison',
                'required' => false
            ])
            ->add('isComplet', CheckboxType::class, [
                'label' => 'Disponible en jeu d\'occasion',
                'required' => false
            ])
            ->add('venteDirecte', CheckboxType::class, [
                'label' => 'Disponible en ventes directes',
                'required' => false
            ])
            ->add('isDeee', CheckboxType::class, [
                'label' => 'Jeu DEEE',
                'required' => false
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
