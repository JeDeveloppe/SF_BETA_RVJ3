<?php

namespace App\Form;

use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PartenaireVilleAutocompleteField extends AbstractType
{


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Ville::class,
            'placeholder' => 'exemple:   FR: Caen - 14000',
            'choice_label' => function (Ville $ville) {
                return $ville->getPays().': ' .$ville->getVilleNom() . ' - ' . $ville->getVilleCodePostal();
            },
            'no_more_results_text' => 'PAS PLUS DE RESULTATS',
            'searchable_fields' => ['villeNom','pays','villeCodePostal'],
            'query_builder' => function(VilleRepository $villeRepository) {
                // return $villeRepository->createQueryBuilder('ville');
                return $villeRepository->createQueryBuilder('v');
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
