<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Document;
use App\Entity\MethodeEnvoi;
use App\Entity\DocumentLignes;
use App\Form\MethodeEnvoiType;
use App\Service\DocumentService;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentsController extends AbstractController
{
    /**
     * @Route("/admin/document/lecture-demande/{slug}", name="document_demande")
     */
    public function lectureDemande($slug, PanierRepository $panierRepository, InformationsLegalesRepository $informationsLegalesRepository): Response
    {

        $paniers = $panierRepository->findBy(['etat' => $slug]);

        $methodeEnvoi = new MethodeEnvoi();
        $form = $this->createForm(MethodeEnvoiType::class, $methodeEnvoi);

        if($paniers == null){
            //on signal le changement
            $this->addFlash('warning', 'État inconnu!');
            return $this->redirectToRoute('admin_accueil');
        }else{
            $informationsLegales = $informationsLegalesRepository->findAll();
            $tva = $informationsLegales[0]->getTauxTva();

            $panier_occasions = $panierRepository->findBy(['etat' => $slug, 'boite' => null]);
            $panier_boites = $panierRepository->findBy(['etat' => $slug, 'occasion' => null]);

            //ON FAIT LE TOTAL DES OCCASIONS
            $totalOccasions = 0;
            foreach($panier_occasions as $panier_occasion){
                $totalOccasions = $totalOccasions + $panier_occasion->getOccasion()->getPriceHt();
            }
        }

        return $this->render('admin/documents/creation_devis.html.twig', [
            'paniers' => $paniers,
            'panier_occasions' => $panier_occasions,
            'panier_boites' => $panier_boites,
            'tva' => $tva,
            'form' => $form->createView(),
            'totalOccasions' => $totalOccasions
        ]);
    }

     /**
     * @Route("/admin/document/creation-devis/{demande}/{user}", name="document_creation_devis")
     */
    public function creationDevis(
        $demande,
        $user,
        Request $request,
        PanierRepository $panierRepository,
        InformationsLegalesRepository $informationsLegalesRepository,
        UserRepository $userRepository,
        DocumentService $documentService,
        EntityManagerInterface $em
        ): Response
    {


        $paniers = $panierRepository->findBy(['user' => $user, 'etat' => $demande]);

        if($paniers == null){
            //on signal le changement
            $this->addFlash('warning', 'Demande inconnue!');
            return $this->redirectToRoute('admin_accueil');
        }else{
            $informationsLegales = $informationsLegalesRepository->findAll();
            $tva = $informationsLegales[0]->getTauxTva();

            //il faudra trouver le dernier document de la base et incrementer de 1 pour le devis

            //puis on met dans la base
            $document = new Document();

            $document->setUser($userRepository->find($user))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setTotalTTC($request->request->get('totalGeneralTTC') * 100)
                    ->setTotalHT($request->request->get('totalGeneralHT') * 100)
                    ->setTauxTva($tva * 100 -100)
                    ->setTotalLivraison($request->request->get('totalLivraison') * 100)
                    ->setIsRelanceDevis(false)
                    ->setAdresseFacturation($paniers[0]->getFacturation())
                    ->setAdresseLivraison($paniers[0]->getLivraison())
                    ->setToken($documentService->generateRandomString())
                    ->setNumeroDevis(1);

            $em->persist($document);
            $em->flush();

            $lignesDemandeBoite = $request->request->get('prixLigne');

            $panier_occasions = $panierRepository->findBy(['etat' => $demande,'user' => $user, 'boite' => null]);
            $panier_boites = $panierRepository->findBy(['etat' => $demande,'user' => $user, 'occasion' => null]);

            foreach($panier_boites as $key=>$panier){
                $documentLigne = new DocumentLignes();

                $documentLigne->setBoite($panier->getBoite())
                              ->setDocument($document)
                              ->setMessage($panier->getMessage())
                              ->setPrixVente($lignesDemandeBoite[$key] * 100);
                $em->persist($documentLigne);
            }

            foreach($panier_occasions as $panier){
                $documentLigne = new DocumentLignes();

                $documentLigne->setBoite($panier->getBoite())
                                ->setDocument($document)
                                ->setOccasion($panier->getOccasion())
                                ->setPrixVente($panier->getOccasion()->getPriceHt() * 100);
                $em->persist($documentLigne);
            }

            //on met en BDD les differentes lignes
            $em->flush();

            dd("FAIRE LA SUITE, SUPPRIMER PANIER");

        }

        return $this->redirectToRoute('devis');

    }
}
