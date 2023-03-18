<?php

namespace App\Service;

use Fpdf\Fpdf;
use DateInterval;
use Dompdf\Dompdf;
use App\Entity\User;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Document;
use App\Entity\DocumentLignes;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MethodeEnvoiRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\DocumentLignesRepository;
use App\Repository\InformationsLegalesRepository;

class DocumentService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private DocumentLignesRepository $documentLignesRepository,
        private EntityManagerInterface $em,
        private InformationsLegalesRepository $informationsLegalesRepository,
        private MethodeEnvoiRepository $methodeEnvoiRepository,
        private UserRepository $userRepository,
        private PanierRepository $panierRepository,
        private ConfigurationRepository $configurationRepository,
        private Utilities $utilities,
        private Environment $templating
        )
    {
    }

    public function generateRandomString($length = 250, $characters = '0123456789abcdefghijklmnopqrstuvwxyz@!_ABCDEFGHIJKLMNOPQRSTUVWXYZ'){
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
        $informationsLegales = $this->informationsLegalesRepository->findOneBy([]);
        $configuration = $this->configurationRepository->findOneBy([]);

        $taux = $informationsLegales->getTauxTva();

        //ON genere un nouveau numero
        $newNumero = $this->generateNewNumberOf("numeroDevis", "getNumeroDevis");

        //puis on met dans la base
        $document = new Document();
        $now = new DateTimeImmutable();
        $endDevis = $now->add(new DateInterval('P'.$configuration->getDevisDelayBeforeDelete().'D'));

        $document->setUser($this->userRepository->find($user))
                ->setCreatedAt($now)
                ->setTotalTTC($request->request->get('totalGeneralTTC') * 100)
                ->setTotalHT($request->request->get('totalGeneralHT') * 100)
                ->setTauxTva($taux)
                ->setTotalLivraison($this->utilities->prixTtcToCentsHt($request->request->get('totalLivraisonTTC'),$taux))
                ->setIsRelanceDevis(false)
                ->setIsDeleteByUser(false)
                ->setAdresseFacturation($paniers[0]->getFacturation())
                ->setAdresseLivraison($paniers[0]->getLivraison())
                ->setToken($this->generateRandomString())
                ->setNumeroDevis($newNumero)
                ->setEndValidationDevis($endDevis)
                ->setCost($request->request->get('totalCostTTC') * 100)
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
                          ->setPrixVente($this->utilities->prixTtcToCentsHt($lignesDemandeBoitePrix[$key],$taux))
                          ->setReponse($lignesDemandeBoiteReponse[$key]);
            $this->em->persist($documentLigne);
        }

        foreach($panier_occasions as $panier){
            $documentLigne = new DocumentLignes();

            $documentLigne->setOccasion($panier->getOccasion())
                            ->setDocument($document)
                            ->setPrixVente($panier->getOccasion()->getPriceHt());
            $this->em->persist($documentLigne);
        }

        //on met en BDD les differentes lignes
        $this->em->flush();

        //et on return le token du document
        return $document->getToken();
    }

    public function fromPanierSaveDevisInDataBaseOnlyOccasions($user, $setup, $paniers, $demande){
        $informationsLegales = $this->informationsLegalesRepository->findOneBy([]);
        $tva = $this->utilities->calculTauxTva($informationsLegales->getTauxTva());
        $methodeEnvoi = $this->methodeEnvoiRepository->findOneBy(['id' => 3]);
        $livraison = $setup['adresseLivraison'];
        $facturation = $setup['adresseFacturation'];

        //ON genere un nouveau numero
        $newNumero = $this->generateNewNumberOf("numeroDevis", "getNumeroDevis");

        //puis on met dans la base
        $document = new Document();
        $now = new DateTimeImmutable();
        $endDevis = $now->add(new DateInterval('P3D'));


        $document->setUser($user)
                ->setCreatedAt($now)
                ->setTotalTTC(($setup['totalOccasionsHT'] * $tva) + ($setup['cost'] * 100)) //on ajoute le cout adhésion
                ->setTotalHT($setup['totalOccasionsHT'] + ($setup['cost'] / $tva * 100)) //on ajoute le cout adhésion
                ->setTauxTva($informationsLegales->getTauxTva())
                ->setCost($setup['cost'] * 100)
                ->setTotalLivraison(0)
                ->setIsDeleteByUser(false)
                ->setIsRelanceDevis(false)
                ->setAdresseLivraison($livraison->getFirstName().' '.$livraison->getLastName().'<br/>'.$livraison->getAdresse().'<br/>'.$livraison->getVille()->getVilleCodePostal().' '.$livraison->getVille()->getVilleNom().'<br/>'.$livraison->getVille()->getDepartement()->getPays()->getIsoCode())
                ->setAdresseFacturation($facturation->getFirstName().' '.$facturation->getLastName().'<br/>'.$facturation->getAdresse().'<br/>'.$facturation->getVille()->getVilleCodePostal().' '.$facturation->getVille()->getVilleNom().'<br/>'.$facturation->getVille()->getDepartement()->getPays()->getIsoCode())
                ->setToken($setup['token'])
                ->setNumeroDevis($newNumero)
                ->setEndValidationDevis($endDevis)
                ->setEnvoi($methodeEnvoi);
        $this->em->persist($document);
        $this->em->flush();

        // $panier_occasions = $this->panierRepository->findBy(['etat' => $demande,'user' => $user, 'boite' => null]);

        foreach($paniers as $panier){
            $documentLigne = new DocumentLignes();

            $documentLigne->setBoite($panier->getBoite())
                            ->setDocument($document)
                            ->setOccasion($panier->getOccasion())
                            ->setPrixVente($panier->getOccasion()->getPriceHt());
            $this->em->persist($documentLigne);
        }

        //on met en BDD les differentes lignes
        $this->em->flush();

        //on supprime les lignes dans le panier
        $this->deletePanierFromUser($paniers);

        //et on return le numero du devis
        return $setup['token'];
    }

    // public function factureToPdf($token){

    //     //on cherche le devis par le token
    //     $doc = $this->documentRepository->findOneBy(['token' => $token]);

    //     //on recupere le taux de tva
    //     $tva = $doc->getTauxTva();

    //     //on recupere les info legales
    //     $infosLegales = $this->informationsLegalesRepository->findAll();
    //     $rfj = $infosLegales[0];

    //     //on recupere les occasions et les pieces detachees facturees
    //     $occasions = $this->documentLignesRepository->findBy(['document' => $doc, 'boite' => null]);
    //     $boites = $this->documentLignesRepository->findBy(['document' => $doc, 'occasion' => null]);

    //     //ON FAIT LE TOTAL DES OCCASIONS
    //     $totalOccasions = 0;
    //     foreach($occasions as $occasion){
    //         $totalOccasions = $totalOccasions + $occasion->getOccasion()->getPriceHt();
    //     }

    //     //ON FAIT LE TOTAL DES PIECES DETACHEES
    //     $totalDetachees = 0;
    //     foreach($boites as $boite){
    //         $totalDetachees = $totalDetachees + $boite->getPrixVente();
    //     }

    //     // class PDF extends Fpdf
    //     // {
    //     //     // En-tête
    //     //     function Header(){
    //     //         // Logo
    //     //         $this->Image('../images/design/refaitesvosjeux.png',10,6,30);
    //     //         // Police Arial gras 15
    //     //         $this->SetFont('Arial','B',15);
    //     //         // Décalage à droite
    //     //         $this->Cell(150);
    //     //         // Titre
    //     //         $this->Cell(30,10,'FACTURE',1,0,'C');
    //     //         // Saut de ligne
    //     //         $this->Ln(20);
    //     //     }
    
    //     //     // Pied de page
    //     //     function Footer(){
    //     //         // Positionnement à 1,5 cm du bas
    //     //         $this->SetY(-15);
    //     //         // Police Arial italique 8
    //     //         $this->SetFont('Arial','I',8);
    //     //         // Numéro de page
    //     //         $this->Cell(0,10,'Page '.$this->PageNo().'/1',0,0,'C');
    //     //     }
    
    
    //     // }


    //      // Instanciation de la classe dérivée
    //     $pdf = new Fpdf('P','mm','A4');
    //     $pdf->AddPage();
    //     $pdf->SetFont('Helvetica','',11);
    //     $pdf->SetTextColor(0);

    //     //HEADER
    //     $pdf->Image('build/images/design/logoRVJ3.png',10,6,30);
    //     // Police Arial gras 15
    //     $pdf->SetFont('Arial','B',15);
    //     // Décalage à droite
    //     $pdf->Cell(150);
    //     // Titre
    //     $pdf->Cell(30,10,'FACTURE',1,0,'C');
    //     // Saut de ligne
    //     $pdf->Ln(20);

    //     // Infos de la socièté sous le logo à gauche
    //     $pdf->SetFont('Helvetica','',8);
    //     $pdf->Text(10,35,utf8_decode('Adresse : '.$rfj->getAdresseSociete()));
    //     $pdf->Text(10,40,'Siret : '.$rfj->getSiretSociete());

    //     $pdf->SetFont('Helvetica','',11);
    //     // Infos de la commande calées à gauche
    //     $pdf->Text(8,78,utf8_decode('N° de facture : '.$doc->getNumeroFacture()));
    //     $pdf->Text(8,84,'Date : '.$doc->getPaiement()->getTimeTransaction()->format('d-m-Y'));
    //     $pdf->Text(8,90,utf8_decode('Mode de règlement : '.$doc->getPaiement()->getMoyenPaiement()));

    //     // Infos du client calées à droite
    //     $adresses = explode('<br/>',$doc->getAdresseFacturation());

    //     $h = 38;
    //     foreach($adresses as $adresse){
    //         $pdf->Text(130,$h,utf8_decode($adresse));
    //         $h += 8;
    //     }
  

    //     //EN TETE DU TABLEAU
    //     $position_entete_produits = 105;
    //     // entete_table_accessoires($position_entete_produits);

    //     $positionLigneAchat = 8;

    //     if(count($occasions) > 0){
    //         foreach($occasions as $ligneAchat){
    //             $pdf->SetY($position_entete_produits + $positionLigneAchat);
    //             $pdf->SetX(8);
    //             $pdf->MultiCell(168,8,utf8_decode("Jeu d'occasion: ".$ligneAchat->getOccasion()->getBoite()->getNom().' - '.$ligneAchat->getOccasion()->getBoite()->getEditeur()),1,'C');
    //             $pdf->SetY($position_entete_produits + $positionLigneAchat);
    //             $pdf->SetX(176);
    //             $pdf->MultiCell(24,8,number_format($ligneAchat->getOccasion()->getPriceHt() / 100 * $tva,2),1,'R');
    //             $positionLigneAchat += 8;
    //         }
    //     }

    //     $position_detail = $position_entete_produits + $positionLigneAchat; // Position à 9mm de l'entête

    //     //LIGNE FOURNITURES
    //     if(count($boites) > 0){
    //         $pdf->SetY($position_detail);
    //         $pdf->SetX(8);
    //         $pdf->MultiCell(168,8,utf8_decode("Fourniture(s) de pièce(s)"),1,'C');
    //         $pdf->SetY($position_detail);
    //         $pdf->SetX(176);
    //         $pdf->MultiCell(24,8,number_format($totalDetachees / 100,2),1,'R');
    //     }else{
    //         $position_detail -= 8;
    //     }

 
    //     //LIGNE LIVRAISON
    //     $livraisons = explode('<br/>',$doc->getAdresseLivraison());

    //     $destinationLivraisonOuRetrait = '';

    //     foreach($livraisons as $livraison){
    //         $destinationLivraisonOuRetrait .= $livraison.' ';
    //     }

    //     $pdf->SetY($position_detail + 16);
    //     $pdf->SetX(8);
    //     if($doc->getTotalLivraison() == 0){
    //         $pdf->MultiCell(168,8,utf8_decode("RETRAIT: ".$destinationLivraisonOuRetrait),1,'R');
    //     }else{
    //         $pdf->MultiCell(168,8,utf8_decode("LIVRAISON: ".$destinationLivraisonOuRetrait),1,'R');
    //     }
    //     $pdf->SetY($position_detail + 16);
    //     $pdf->SetX(176);
    //     $pdf->MultiCell(24,8,number_format($doc->getTotalLivraison() / 100,2),1,'R');



    //     //tableau des totaux
    //     $tableauTotauxY = $position_detail + 50;
    //     $pdf->SetY($tableauTotauxY);
    //     $pdf->SetX(148);
    //     $pdf->MultiCell(28,8,"Total HT:",1,'L');
    //     $pdf->SetY($tableauTotauxY);
    //     $pdf->SetX(176);
    //     $pdf->MultiCell(24,8,number_format($doc->getTotalHT() / 100,"2",".",""),1,'R');
    //     $pdf->SetY($tableauTotauxY + 8);
    //     $pdf->SetX(148);
    //     $pdf->MultiCell(28,8,"TVA:",1,'L');
    //     $pdf->SetY($tableauTotauxY + 8);
    //     $pdf->SetX(176);
    //     $pdf->MultiCell(24,8,number_format($doc->getTotalTTC() / 100,"2",".",""),1,'R');
    //     $pdf->SetY($tableauTotauxY + 16);
    //     $pdf->SetX(148);
    //     $pdf->MultiCell(28,8,"Total TTC:",1,'L');
    //     $pdf->SetY($tableauTotauxY + 16);
    //     $pdf->SetX(176);
    //     $pdf->MultiCell(24,8,number_format($doc->getTotalTTC() / 100,"2",".",""),1,'R');

    //     //LIGNE REMERCIEMENT
    //     $pdf->SetFont('Helvetica','',12);
    //     $pdf->SetY(250);
    //     $pdf->SetX(10);
    //     $pdf->MultiCell(190,8,utf8_decode("MERCI POUR VOTRE COMMANDE."),0,'C');

    //     //ligne TVA dans table de config vaut 1 = PAS DE TVA
    //     if($doc->getTauxTva() == 0){
    //     $pdf->SetFont('Helvetica','',8);
    //     $pdf->SetY(262);
    //     $pdf->SetX(10);
    //     $pdf->MultiCell(190,8,utf8_decode("TVA non applicable, article 293B du code général des impôts."),0,'C');
    //     }

    //     //FOOTER
    //     // Positionnement à 1,5 cm du bas
    //     $pdf->SetY(-35);
    //     // Police Arial italique 8
    //     $pdf->SetFont('Arial','I',8);
    //     // Numéro de page
    //     $pdf->Cell(0,10,'Page '.$pdf->PageNo().'/1',0,0,'C');


    //     // Nom du fichier
    //     $nom = 'RefaitesVosJeux-'.$doc->getNumeroFacture().'.pdf';
    //     // Création du PDF
    //     $pdf->Output($nom,'I');
    // }

    public function factureToPdf($token){
        //on cherche le devis par le token
        $doc = $this->documentRepository->findOneBy(['token' => $token]);

        //on recupere les info legales
        $infosLegales = $this->informationsLegalesRepository->findBy([]);

        //on recupere le client
        $user =  $doc->getUser();

        //on recupere les occasions et les pieces detachees facturees
        $occasions = $this->documentLignesRepository->findBy(['document' => $doc, 'boite' => null]);
        $boites = $this->documentLignesRepository->findBy(['document' => $doc, 'occasion' => null]);

        //ON FAIT LE TOTAL DES OCCASIONS
        $totalOccasions = 0;
        foreach($occasions as $occasion){
            $totalOccasions = $totalOccasions + $occasion->getOccasion()->getPriceHt();
        }

        //ON FAIT LE TOTAL DES PIECES DETACHEES
        $totalDetachees = 0;
        foreach($boites as $boite){
            $totalDetachees = $totalDetachees + $boite->getPrixVente();
        }

        $html = $this->templating->render("commun/template_facture.html.twig", [
            'doc' => $doc,
            'infosLegales' => $infosLegales,
            'user' => $user,
            'occasions' => $occasions,
            'boites' => $boites,
            'totalOccasions' => $totalOccasions,
            'totalDetachees' => $totalDetachees
        ]);

        // $html .= '<style>'.file_get_contents("../public/build/site.95d9ef6a.css").'</style>';
   
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->set_base_path("/www/public/build/");
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("Facture-".$doc->getNumeroFacture().".pdf");
    }
}