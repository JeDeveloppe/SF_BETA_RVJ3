<?php

namespace App\Form;

use App\Entity\Boite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BoiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageblob', FileType::class, [
                'data_class' => null,
                'required' => false,
                'label' => "Choisir une image:"
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de la boite:'
            ])
            ->add('editeur')
            ->add('annee', TextType::class, [
                'label' => 'Année:'
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'Slug (nom-de-la-boite):'
            ])
            ->add('poidBoite', TextType::class, [
                'label' => 'Poid de la boite (g):'
            ])
            ->add('age', TextType::class, [
                'label' => 'A partir de: (âge)'
            ])
            ->add('nbrJoueurs', TextType::class, [
                'label' => 'Nombre de joueurs:'
            ])
            ->add('prixHt', TextType::class, [
                'label' => 'Prix de vente TTC:'
            ])
            ->add('contenu', TextType::class, [
                'label' => 'Contenu de la boite:'
            ])
            ->add('message', TextType::class, [
                'label' => 'Message spécial:'
            ])
            ->add('isOnLine', CheckboxType::class, [
                'label' => 'En ligne (cocher)'
            ])
            ->add('isLivrable', CheckboxType::class, [
                'label' => 'Disponible en livraison'
            ])
            ->add('isComplet', CheckboxType::class, [
                'label' => 'Disponible en jeu d\'occasion'
            ])
            ->add('isDeee', CheckboxType::class, [
                'label' => 'Jeu DEEE'
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
