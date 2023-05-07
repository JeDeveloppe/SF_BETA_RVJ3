<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Entity\Boite;
use App\Entity\Category;
use App\Entity\Envelope;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'article:'
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité total en stock:'
            ])
            ->add('priceHt', NumberType::class, [
                'label' => 'Prix HT en cents:'
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poid en gramme:',
                'required' => true
            ])

            ->add('envelope', EntityType::class, [
                'label' => 'Enveloppe pour envoi:',
                'class' => Envelope::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->createQueryBuilder('b')
                //         ->orderBy('b.nom', 'ASC');
                // },
                'choice_label' => function ($envelope) {
                    return $envelope->getName();
                },
                'multiple' => false,
                // 'expanded' => true,
                'required' => false
                // 'mapped' => false
            ])
            ->add('dimension', TextType::class, [
                'label' => 'Dimensions:',
                'attr' => [
                    'placeholder' => "Champs de texte libre"
                ]
            ])
            ->add('boite', EntityType::class, [
                'label' => 'Autres boites de jeu:',
                'class' => Boite::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->createQueryBuilder('b')
                //         ->orderBy('b.nom', 'ASC');
                // },
                'choice_label' => function ($boite) {
                    return $boite->getNom().' - '.$boite->getEditeur().' - '.$boite->getAnnee();
                },
                'multiple' => true,
                // 'expanded' => true,
                'autocomplete' => true,
                'required' => false
                // 'mapped' => false
            ])
            ->add('boiteRelative', EntityType::class, [
                'label' => 'Boites similaires:',
                'class' => Boite::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->createQueryBuilder('b')
                //         ->orderBy('b.nom', 'ASC');
                // },
                'choice_label' => function ($boite) {
                    return $boite->getNom().' - '.$boite->getEditeur().' - '.$boite->getAnnee();
                },
                'multiple' => true,
                // 'expanded' => true,
                'autocomplete' => true,
                'required' => false
                // 'mapped' => false
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'boite' => 'boite'
        ]);
    }
}
