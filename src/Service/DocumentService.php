<?php

namespace App\Service;

use DateInterval;
use DateTimeImmutable;
use App\Entity\Document;
use App\Entity\DocumentLignes;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MethodeEnvoiRepository;
use App\Repository\InformationsLegalesRepository;
use App\Controller\Admin\InformationsLegalesController;

class DocumentService
{
    private $documentRepository;
    private $em;
    private $informationsLegalesRepository;
    private $userRepository;
    private $panierRepository;

    public function __construct(
        DocumentRepository $documentRepository,
        EntityManagerInterface $em,
        InformationsLegalesRepository $informationsLegalesRepository,
        MethodeEnvoiRepository $methodeEnvoiRepository,
        UserRepository $userRepository,
        PanierRepository $panierRepository
        )
    {
        $this->documentRepository = $documentRepository;
        $this->em = $em;
        $this->informationsLegalesRepository = $informationsLegalesRepository;
        $this->methodeEnvoiRepository = $methodeEnvoiRepository;
        $this->userRepository = $userRepository;
        $this->panierRepository = $panierRepository;
    }

    public function generateRandomString($length = 250, $characters = '0123456789abcdefghijklmnopqrstuvwxyz@!_ABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $charactersLength = strlen($characters);
        $randomString = "";
        for($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength-1)];
        }
        return $randomString;
    }

    public function generateNewNumberOf($column, $methode){

        $dateTimeImmutable = new DateTimeImmutable('now');
        $year = $dateTimeImmutable->format('Y');
        $month = $dateTimeImmutable->format('m');

        //il faudra trouver le dernier document de la base et incrementer de 1 pour le document
        $lastDocumentByYear = $this->documentRepository->findLastEntryFromThisYear($column, $year);
  
        //si pas d'entree alors nouvelle annee
        if(count($lastDocumentByYear) == 0){
            
            $numero = 1;
            return $this->numberConstruction($numero,$year,$month);

        }else{

            //dernier entree on recupere le numero de devis
            $numero = substr($lastDocumentByYear[0]->$methode(), -4) + 1; //2022010001 reste 0001 + 1

            return $this->numberConstruction($numero,$year,$month);
        }
       
    }

    public function numberConstruction($numero,$year,$month){
        
        if($numero == 1){ //premier enregistrement de l'annee
            return $year.$month.'0001';
        }else{
            $longueur = strlen($numero); //dernier enregistrement

            if($longueur < 2){                        //moins de 10
                    $numeroCreer = $year.$month."000".$numero;
            }else if($longueur == 2){                 //de 10 à 99
                    $numeroCreer = $year.$month."00".$numero;
            }else if($longueur == 3){                 //de 100 à 999
                    $numeroCreer = $year.$month."0".$numero;
            }else if($longueur == 4){                 //de 1000 à 9999
                    $numeroCreer = $year.$month.$numero;
            }
            return $numeroCreer;
        }
    }

    public function deletePanierFromUser($paniers){
        
        foreach($paniers as $panier){
            $this->em->remove($panier);
        }
        $this->em->flush();
    }

    public function saveDevisInDataBase($user, $request, $paniers, $demande){
        $informationsLegales = $this->informationsLegalesRepository->findAll();
        $tva = $informationsLegales[0]->getTauxTva();

        //ON genere un nouveau numero
        $newNumero = $this->generateNewNumberOf("numeroDevis", "getNumeroDevis");

        //puis on met dans la base
        $document = new Document();
        $now = new DateTimeImmutable();
        $endDevis = $now->add(new DateInterval('P1D'));

        $document->setUser($this->userRepository->find($user))
                ->setCreatedAt($now)
                ->setTotalTTC($request->request->get('totalGeneralTTC') * 100)
                ->setTotalHT($request->request->get('totalGeneralHT') * 100)
                ->setTauxTva($tva * 100 -100)
                ->setTotalLivraison($request->request->get('totalLivraisonTTC') * 100)
                ->setIsRelanceDevis(false)
                ->setAdresseFacturation($paniers[0]->getFacturation())
                ->setAdresseLivraison($paniers[0]->getLivraison())
                ->setToken($this->generateRandomString())
                ->setNumeroDevis($newNumero)
                ->setEndValidationDevis($endDevis)
                ->setEnvoi($this->methodeEnvoiRepository->find($request->request->get('envoi')));

        $this->em->persist($document);
        $this->em->flush();

        $lignesDemandeBoitePrix = $request->request->get('prix');
        $lignesDemandeBoiteReponse = $request->request->get('reponse');

        $panier_occasions = $this->panierRepository->findBy(['etat' => $demande,'user' => $user, 'boite' => null]);
        $panier_boites = $this->panierRepository->findBy(['etat' => $demande,'user' => $user, 'occasion' => null]);

        foreach($panier_boites as $key=>$panier){
            $documentLigne = new DocumentLignes();

            $documentLigne->setBoite($panier->getBoite())
                          ->setDocument($document)
                          ->setMessage($panier->getMessage())
                          ->setPrixVente($lignesDemandeBoitePrix[$key] * 100)
                          ->setReponse($lignesDemandeBoiteReponse[$key]);
            $this->em->persist($documentLigne);
        }

        foreach($panier_occasions as $panier){
            $documentLigne = new DocumentLignes();

            $documentLigne->setBoite($panier->getBoite())
                            ->setDocument($document)
                            ->setOccasion($panier->getOccasion())
                            ->setPrixVente($panier->getOccasion()->getPriceHt() * 100);
            $this->em->persist($documentLigne);
        }

        //on met en BDD les differentes lignes
        $this->em->flush();

        //et on return le numero du devis
        return $newNumero;
    }
}