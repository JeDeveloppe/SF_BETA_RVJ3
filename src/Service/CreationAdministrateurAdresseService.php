<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Adresse;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class CreationAdministrateurAdresseService
{
    public function __construct(
        private AdresseRepository $adresseRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private PaysRepository $paysRepository,
        private VilleRepository $villeRepository
        ){
    }

    public function creationAdminAdresse(SymfonyStyle $io): void
    {
        //on vérifié si pn a déjà créer l'administrateur spécial
        $user = $this->userRepository->findOneBy(['email' => 'ADMINISTRATION@ADMINISTRATION.FR']);

        if(is_null($user)){
            $io->title('Création de l\'user ADMINISTRATION');

            //on rentre un user "ADMINISTRATEUR"
            $user = new User();
            $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                    $user,
                    $this->documentService->generateRandomString(25)
                )
            );
            $user->setCreatedAt(new DateTimeImmutable('now'))
                ->setEmail("ADMINISTRATION@ADMINISTRATION.FR")
                ->setPhone(0600000000)
                ->setCountry($this->paysRepository->findOneBy(['isoCode' => 'FR']))
                ->setDepartment(14);
    
            $this->manager->persist($user);
            $this->manager->flush();

            $io->title('Création de l\'adresse de retrait');

            //on rentre l'adresse de retrait
            $adresse = new Adresse();
            $adresse->setLastName("COOP")
            ->setFirstName("100%")
            ->setAdresse("33 route de Trouville")
            ->setVille($this->villeRepository->findOneBy(['villeCodePostal' => 14000, 'villeNom' => 'CAEN']))
            ->setUser($user)
            ->setIsFacturation(false);

            $this->manager->persist($adresse);
            $this->manager->flush();

            $io->success('Importation terminée');

        }
    }

}