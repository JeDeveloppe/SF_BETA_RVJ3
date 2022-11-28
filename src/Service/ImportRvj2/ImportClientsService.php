<?php

namespace App\Service\ImportRvj2;

use App\Entity\User;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Repository\UserRepository;
use App\Repository\PaysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportClientsService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private PaysRepository $paysRepository
        ){
    }

    public function importClients(SymfonyStyle $io): void
    {
        $io->title('Importation des clients');

        $clients = $this->readCsvFileClients();
        
        $io->progressStart(count($clients));

        foreach($clients as $arrayClient){
            $io->progressAdvance();
            $client = $this->createOrUpdateClient($arrayClient);
            $this->em->persist($client);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileClients(): Reader
    {
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/clients.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateClient(array $arrayClient): User
    {
        $client = $this->userRepository->findOneBy(['token' => $arrayClient['idUser']]);

        if(!$client){
            $client = new User();
        }

        $client->setEmail($arrayClient['email'])
                ->setRvj2Id($arrayClient['idClient'])
                ->setPassword($arrayClient['password'])
                ->setNickname($arrayClient['pseudo'])
                ->setPhone($arrayClient['telephone'])
                ->setLevel($arrayClient['userLevel'])
                ->setToken($arrayClient['idUser'])
                ->setMembership($this->getDateTimeImmutableFromTimestamp($arrayClient['isAssociation']))
                ->setDepartment(substr($arrayClient['cpLivraison'],0,2))
                ->setCountry($this->paysRepository->findOneBy(['isoCode' => $arrayClient['paysFacturation']]));

                if($arrayClient['timeInscription'] != 0){
                    $time = (int) $arrayClient['timeInscription'];
                    $client->setCreatedAt($this->getDateTimeImmutableFromTimestamp($time));
                }

        return $client;
    }

    private function getDateTimeImmutableFromTimestamp($timestamp)
    {
        if($timestamp !== null){
            $tps = (int) $timestamp;
            $date = new DateTimeImmutable();
    
            return $date->setTimestamp($tps);
        }else{
            return null;
        }

    }

}