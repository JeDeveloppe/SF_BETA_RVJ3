<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Ville;
use App\Repository\PaysRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use App\Repository\VilleRepository;
use Symfony\Component\Form\FormInterface;
use App\Repository\VilleBelgiqueRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    private $villeRepository;

    public function __construct(VilleRepository $villeRepository)
    {
        $this->villeRepository = $villeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => "Email:"
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un mot de passe valide !',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Minimum {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('plainPassword2', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un mot de passe valide !',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Minimum {{ limit }} charactères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => "Téléphone:"
            ])
            ->add('country', EntityType::class,  [
                'class' => Pays::class,
                'placeholder' => 'Choisir un pays...',
                'label' => 'Pays:',
                'choice_label' => 'name'
            ])
            ->add('department', ChoiceType::class,  [
                'choice_label' => 'villeDepartement',
                'disabled' => true,
                'placeholder' => 'Merci de choisir un pays...',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "",
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        ;

        $isCountryModifier = function (FormInterface $form, Pays $pays = null) {

                $isoCode = (null === $pays) ? [] : $pays->getIsoCode();
                
                if($isoCode == "FR"){
                    $departments = $this->villeRepository->findDepartmentsByPays();
                    $form
                        ->add('department', EntityType::class,  [
                            'class' => Ville::class,
                            'choice_label' => 'villeDepartement',
                            'choices' => $departments,
                            'placeholder' => 'Choisissez un département...',
                            'constraints' => new NotBlank(['message' => 'Choisissez un département'])
                        ]);
                }else if($isoCode == "BE"){
                    $departments = $this->villeBelgiqueRepository->findDepartmentsByPays();
                        $form->add('department', EntityType::class,  [
                            'class' => VilleBelgique::class,
                            'choice_label' => 'villeProvince',
                            'choices' => $departments,
                            'placeholder' => 'Choisissez une province...',
                            'constraints' => new NotBlank(['message' =>'Choisissez une province...' ])
                        ]);
                }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($isCountryModifier) {
                $isCountryModifier($event->getForm(), $event->getData());
            }
        );

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($isCountryModifier) {
                $country = $event->getForm()->getData();
                $isCountryModifier($event->getForm()->getParent(), $country);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
