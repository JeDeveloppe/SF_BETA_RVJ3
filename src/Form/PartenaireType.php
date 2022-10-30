<?php

namespace App\Form;

use App\Entity\Partenaire;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\DepartementRepository;
use App\Form\PartenaireVilleAutocompleteField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PartenaireType extends AbstractType
{

    private $villeRepository;
    private $paysRepository;

    public function __construct(VilleRepository $villeRepository, PaysRepository $paysRepository, DepartementRepository $departementRepository)
    {
        $this->villeRepository = $villeRepository;
        $this->paysRepository = $paysRepository;
        $this->departementRepository = $departementRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom:'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:'
            ])
            ->add('collecte', TextareaType::class, [
                'label' => 'Détails de ce que le partenaire collect:'
            ])
            ->add('vend', TextareaType::class, [
                'label' => 'Détails de ce que le partenaire vend:'
            ])
            ->add('url', UrlType::class, [
                'label' => 'Adresse du site internet:'
            ])
            ->add('imageBlob', FileType::class, [
                'label' => 'Image:',
                'data_class' => null,
                'required' => false,
                'mapped' => false
            ])
            ->add('ville', PartenaireVilleAutocompleteField::class)
            ->add('isDon', CheckboxType::class, [
                'label' => 'Accepte les dons',
                'required' => false
            ])
            ->add('isDetachee', CheckboxType::class, [
                'label' => 'Vend des pièces détachées',
                'required' => false
            ])
            ->add('isComplet', CheckboxType::class, [
                'label' => 'Vend des jeux complet',
                'required' => false
            ])
            ->add('isEcommerce', CheckboxType::class, [
                'label' => 'Est un site e-commerce',
                'required' => false
            ])
            ->add('isOnLine', CheckboxType::class, [
                'label' => 'En ligne sur Refaitesvosjeux.fr',
                'label_attr' => [
                    'class' => 'switch-custom'
                    ],
                'required' => false
            ])

            // ->add('country', EntityType::class, [
            //     'placeholder' => 'Choisir un pays...',
            //     'class' => Pays::class,
            //     'choice_label' => 'name',
            //     'label' => 'Pays:',
            //     'query_builder' => function(EntityRepository $er){
            //         return $er->createQueryBuilder('p')
            //                 ->orderBy('p.name', 'ASC');
            //     }
            // ])

            // $builder->get('country')->addEventListener(
            //     FormEvents::POST_SUBMIT,
            //     function (FormEvent $event){
            //         $form = $event->getForm();
            //         $this->addVilleField($form->getParent(), $event->getForm()->getData());
            //     }
            // );

            // $builder->get('country')->addEventListener(
            //     FormEvents::POST_SUBMIT,
            //     function (FormEvent $event){
            //         $form = $event->getForm();
            //         $this->addDepartementField($form->getParent(), $event->getForm()->getData());
            //     }
            // );

            // $builder->addEventListener(
            //     FormEvents::POST_SET_DATA,
            //     function (FormEvent $event){
            //         $data = $event->getData();
            //         $ville = $data->getVille();
            //         $form = $event->getForm();
            //         if($ville){
            //             $country = $ville->getDepartement()->getPays();
            //             // $departement = $ville->getDepartement();
            //             // $this->addDepartementField($form, $country);
            //             $this->addVilleField($form, $country->getIsoCode());
            //             // $form->get('departement')->setData($departement);
            //             $form->get('ville')->setData($ville);
            //         }else{
            //             // $this->addDepartementField($form, null);
            //             $this->addVilleField($form, null);
            //         }
            //     }
            // );
            ;
    }

    // private function addDepartementField(FormInterface $form, $country){

    //         $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
    //             'departement',
    //             EntityType::class,
    //             null,
    //             [
    //                 'class' => Departement::class,
    //                 'placeholder' => $country ? 'Choisir un département ou région...' : 'En attente du pays...',
    //                 'choice_label' => 'name',
    //                 'choices' => $country ? $country->getDepartements() : [],
    //                 'auto_initialize' => false,
    //                 'mapped' => false,
    //                 'required' => false,
    //                 'label' => 'Département ou province:'
    //             ]
    //         );
    
    //         $builder->addEventListener(FormEvents::POST_SUBMIT,
    //         function(FormEvent $event){
    //                 $form = $event->getForm();
    //                 $this->addVilleField($form->getParent(), $form->getData());
    //         });
            
    //         $form->add($builder->getForm());       
    // }

    // private function addVilleField(FormInterface $form, $isoCode){

    //         // $form->add('ville', EntityType::class, [
    //         //     'class' => Ville::class,
    //         //     'choice_label' => 'ville_nom',
    //         //     'placeholder' => $departement ? 'Choisissez une ville...' : 'En attente du département...',
    //         //     'choices' => $departement ? $this->villeRepository->findVillesFromDepartementOrderByASC($departement) : [],
    //         //     'label' => 'Ville:'
    //         // ]);
    //         $form->add('ville', PartenaireVilleAutocompleteField::class);
    // }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
