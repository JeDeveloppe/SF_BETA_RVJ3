<?php

namespace App\Service\ImportRvj2;

use DateTimeImmutable;
use League\Csv\Reader;
use App\Entity\Document;
use App\Entity\Paiement;
use App\Repository\PaysRepository;
use App\Repository\UserRepository;
use App\Repository\BoiteRepository;
use App\Repository\DocumentRepository;
use App\Repository\OccasionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MethodeEnvoiRepository;
use App\Repository\InformationsLegalesRepository;
use App\Repository\PaiementRepository;
use App\Service\Utilities;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDocumentsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentRepository $documentRepository,
        private PaysRepository $paysRepository,
        private MethodeEnvoiRepository $methodeEnvoiRepository,
        private Utilities $utilities,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private UserRepository $userRepository,
        private OccasionRepository $occasionRepository,
        private BoiteRepository $boiteRepository,
        private PaiementRepository $paiementRepository
        ){
    }

    public function importDocuments(SymfonyStyle $io): void
    {
        $io->title('Importation des documents');

        $docs = $this->readCsvFileDocuments();
        
        $io->progressStart(count($docs));

        foreach($docs as $arrayDoc){
            $io->progressAdvance();
            $document= $this->createOrUpdateDocument($arrayDoc);

            $this->em->persist($document);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDocuments(): Reader
    {
        $csvDocuments = Reader::createFromPath('%kernel.root.dir%/../import/documents.csv','r');
        $csvDocuments->setHeaderOffset(0);

        return $csvDocuments;
    }

    private function createOrUpdateDocument(array $arrayDoc): Document
    {
        $document = $this->documentRepository->findOneBy(['token' => $arrayDoc['validKey']]);

        if(!$document){
            $document = new Document();
        }

        $document
        ->setToken($arrayDoc['validKey'])
        ->setRvj2Id($arrayDoc['idDocument'])
        ->setNumeroDevis((int) substr($arrayDoc['numero_devis'],3))
        ->setNumeroFacture((int) substr($arrayDoc['numero_facture'],3))
        ->setTotalHT($arrayDoc['totalHT'])
        ->setTotalTTC($arrayDoc['totalTTC'])
        ->setTotalLivraison($arrayDoc['prix_expedition'])
        ->setAdresseFacturation($arrayDoc['adresse_facturation'])
        ->setAdresseLivraison($arrayDoc['adresse_livraison'])
        ->setIsRelanceDevis($arrayDoc['relance_devis'])
        ->setEndValidationDevis($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['end_validation']))
        ->setCreatedAt($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time']))
        ->setEnvoiEmailDevis($this->utilities->getDateTimeImmutableFromTimestamp($arrayDoc['time_mail_devis']))
        ->setIsDeleteByUser(false)
        ->setPaiement(null)
        ->setMessage($arrayDoc['commentaire'])
        ->setTauxTva(0)
        ->setCost($arrayDoc['prix_preparation'])
        ->setTokenPaiementRvj2($arrayDoc['num_transaction']);

        if($arrayDoc['expedition'] == "poste"){
            $expedition = $this->methodeEnvoiRepository->find(1);
        }else if($arrayDoc['expedition'] == "mondialRelay"){
            $expedition = $this->methodeEnvoiRepository->find(4);
        }else if($arrayDoc['expedition'] == "retrait_caen1"){
            $expedition = $this->methodeEnvoiRepository->find(3);
        }else if($arrayDoc['expedition'] == "colissimo"){
            $expedition = $this->methodeEnvoiRepository->find(2);
        }else{
            dd('Methode envoi non connue '.$arrayDoc['expedition']);
        }

        $document->setEnvoi($expedition);

        $user = $this->userRepository->findOneBy(['rvj2Id' => (int) $arrayDoc['idUser']]);

        if(!$user){
            $document->setUser($this->userRepository->findOneBy(['email' => 'ADMINISTRATION@ADMINISTRATION.FR']));
        }else{
            $document->setUser($user);
        }

        return $document;
    }
}