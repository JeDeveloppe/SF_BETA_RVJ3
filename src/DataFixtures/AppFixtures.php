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
        $methode->setName('Envoi par La Poste');
        $manager->persist($methode);
        $methode = new MethodeEnvoi();
        $methode->setName('Envoi par Colissimo');
        $manager->persist($methode);
        $methode = new MethodeEnvoi();
        $methode->setName('Retrait sur Caen (COOP 5 pour 100)');
        $manager->persist($methode);

        //on fait la configuration du site
        $conf = new Configuration();
        $conf->setDevisDelayBeforeDelete(5)
            ->setPrefixeDevis('DEV')
            ->setPrefixeFacture('FAC');
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
