<?php

namespace App\Controller\Site;

use App\Entity\User;
use DateTimeImmutable;
use App\Form\RegistrationFormType;
use App\Repository\InformationsLegalesRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\UserAuthentificatorAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private Security $security,
        private PanierRepository $panierRepository)
    {
    }

    /**
     * @Route("/inscription-au-service", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthentificatorAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        ): Response
    {
   
      
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form['plainPassword']->getData() == $form['plainPassword2']->getData()) {
            
            $user = new User();

            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setCreatedAt(new DateTimeImmutable('now'))
                ->setEmail($form['email']->getData())
                ->setPhone($form['phone']->getData())
                ->setCountry($form['country']->getData())
                ->setDepartment($form['department']->getData()->getVilleDepartement());

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);
    }
}