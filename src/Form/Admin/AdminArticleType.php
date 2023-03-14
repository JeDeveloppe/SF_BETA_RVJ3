<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Entity\Boite;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('boite', EntityType::class, [
                'label' => 'Boites de jeu:',
                'class' => Boite::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->createQueryBuilder('b')
                //         ->orderBy('b.nom', 'ASC');
                // },
                'choice_label' => function ($boite) {
                    return '['.$boite->getId().'] '.$boite->getNom().' - '.$boite->getEditeur().' - '.$boite->getAnnee();
                },
                'multiple' => true,
                // 'expanded' => true,
                'autocomplete' => true,
                // 'mapped' => false
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
