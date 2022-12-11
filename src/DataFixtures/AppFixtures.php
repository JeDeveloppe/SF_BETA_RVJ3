<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use App\Entity\MethodeEnvoi;
use App\Entity\Configuration;
use App\Repository\PaysRepository;
use App\Entity\InformationsLegales;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function __construct(
        private PaysRepository $paysRepository)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        //on met les pays:
        $pays = new Pays();
        $pays->setName('FRANCE')->setIsoCode("FR");
        $manager->persist($pays);

        $pays = new Pays();
        $pays->setName('BELGIQUE')->setIsoCode("BE");
        $manager->persist($pays);
        $manager->flush();
        
        //on met les methodes d'envoi'
        $methode = new MethodeEnvoi();
        $methode->setName('Envoi par La Poste'); // id 1
        $manager->persist($methode);
        $methode = new MethodeEnvoi();
        $methode->setName('Envoi par Colissimo'); // id 2
        $manager->persist($methode);
        $methode = new MethodeEnvoi();
        $methode->setName('Retrait sur Caen (COOP 5 pour 100)'); // id 3
        $manager->persist($methode);
        $methode = new MethodeEnvoi();
        $methode->setName('Envoi par Mondial Relay'); // id 4
        $manager->persist($methode);

        //on fait la configuration du site
        $conf = new Configuration();
        $conf->setDevisDelayBeforeDelete(5)
            ->setPrefixeDevis('DEV')
            ->setPrefixeFacture('FAC')
            ->setGrandPlateauBois(300)
            ->setGrandPlateauPlastique(200)
            ->setPetitPlateauBois(200)
            ->setPetitPlateauPlastique(100)
            ->setPieceUnique("De 0,60 à 2,00")
            ->setPieceMultiple("De 0,05 à 0,20")
            ->setPieceMetalBois("De 0,60 à 2,00")
            ->setAutrePiece("De 0,30 à 0,50")
            ->setEnveloppeSimple(120)
            ->setEnveloppeBulle(160)
            ->setCost(200);
        $manager->persist($conf);

        //les informations legales
        $infosLegales = new InformationsLegales();
        $infosLegales->setAdresseSociete('24 rue froide, 14980 ROTS')
                    ->setSiretSociete(00000000)
                    ->setAdresseMailSite('contact@refaitesvosjeux.fr')
                    ->setSocieteWebmaster('Je-Développe')
                    ->setNomWebmaster('Mr WETTA René')
                    ->setNomSociete('Refaites vos jeux')
                    ->setSiteUrl('www.refaitesvosjeux.fr')
                    ->setHebergeurSite('IONOS SARL, 7 PLACE DE LA GARE, 57200 SARREGUEMINES')
                    ->setTauxTva(1.00)
                    ->setCountry($this->paysRepository->findOneBy(['isoCode' => 'FR']));
        $manager->persist($infosLegales);

        $manager->flush();
    }
}
