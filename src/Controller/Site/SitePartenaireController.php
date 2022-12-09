<?php

namespace App\Controller\Site;


use App\Repository\PaysRepository;
use App\Repository\PanierRepository;
use App\Repository\PartenaireRepository;
use App\Repository\DepartementRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SitePartenaireController extends AbstractController
{
    public function __construct(
        private InformationsLegalesRepository $informationsLegalesRepository,
        private Security $security,
        private PanierRepository $panierRepository,
        private Utilities $utilities
    )
    { 
    }
    /**
     * @Route("/partenaires/{pays}", name="carte_partenaires", methods={"GET"})
     */
    public function index(
        DepartementRepository $departementRepository,
        $pays,
        PaysRepository $paysRepository,
        PartenaireRepository $partenaireRepository
        ): Response
    {

        $country = $paysRepository->findOneBy(['name' => $pays]);

        if(!$country) {
            $this->addFlash('danger', 'Pays inconnu!');
            return $this->redirectToRoute('accueil');
        }

        //on cherche les partenaires du pays selectionner ET ville bien renseignée
        $partenaires = $partenaireRepository->findPartenairesFullVisibility($country);

        //garder le tableau des depots
        $depots = [];

        //option liste
        foreach($partenaires as $partenaire){
            $departement = $partenaire->getVille()->getVilleDepartement();

            if(array_key_exists($departement, $depots)){
                $depots[$departement][] = $partenaire;
            }else{
                $depots[$departement] = [];
                $depots[$departement][] = $partenaire;
            }
        }

        return $this->render('site/partenaire/'.strtolower($pays).'/liste-'.strtolower($pays).'.html.twig', [
            'depots' => $depots,
            'infosAndConfig' => $this->utilities->importConfigurationAndInformationsLegales(),
            'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        ]);



        // option carte
        // foreach($partenaires as $partenaire){
        //     $depots[$partenaire->getId()] = $partenaire->getVille()->getVilleDepartement();
        // }
        
        //AFFICHAGE DE LA CARTE :
        //on boucle sur chaque partenaire
        // foreach($partenaires as $key => $partenaire){

        //     //affichage details des ventes
        //     if($partenaire->getIsComplet() == 1 || $partenaire->getIsDetachee() == 1){
        //         $detailsVente= "<b>Le service vend:</b><br/>".$partenaire->getVend();
        //     }else{
        //         $detailsVente = "";
        //     }

        //     if($partenaire->getId() == 10 || $partenaire->getId() == 14){

        //         if($partenaire->getId() == 14){
        //         $size = 45; //35 commun
        //         }else{
        //         $size = 55; //55 pour logo caen
        //         }

        //         array_push($depots,
        //         [
        //         "lat" => $partenaire->getVille()->getLat(),
        //         "lng" => $partenaire->getVille()->getLng(),
        //         "name" => $partenaire->getName().' à '.$partenaire->getVille()->getVilleNom().' ('.$partenaire->getVille()->getVilleDepartement().')',
        //         "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire->getImageBlob().'"/></p><p>'.$partenaire->getDescription().'</p><p><b>Le service collecte:</b><br/>'.$partenaire->getCollecte().'</p><p>'.$detailsVente.'</p>',
        //         "url" => $partenaire->getUrl(),
        //         "type" => "image",
        //         "image_url" => "https://www.refaitesvosjeux.fr/images/design/logoDepots.png",
        //         "size" => $size,
        //         "image_url" => "imgURL"
        //         ]);
        //     }else{
        //         if($partenaire->getIsEcommerce() == 1){
        //             array_push($depots,
        //             [
        //                 "lat" => $partenaire->getVille()->getLat(),
        //                 "lng" => $partenaire->getVille()->getLng(),
        //                 "name" => $partenaire->getName().' à '.$partenaire->getVille()->getVilleNom().' ('.$partenaire->getVille()->getVilleDepartement().')',
        //                 "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire->getImageBlob().'"/></p><p>'.$partenaire->getDescription().'</p><p><b>Le service collecte:</b><br/>'.$partenaire->getCollecte().'</p><p>'.$detailsVente.'</p><p>Site E-commerce disponible.</p>',
        //                 "url" => $partenaire->getUrl(),
        //                 "size" => "35",
        //                 "image_url" => "imgURL"
        //             ]);
        //         }else{
        //             array_push($depots,
        //             [
        //                 "lat" => $partenaire->getVille()->getLat(),
        //                 "lng" => $partenaire->getVille()->getLng(),
        //                 "name" => $partenaire->getName().' à '.$partenaire->getVille()->getVilleNom().' ('.$partenaire->getVille()->getVilleDepartement().')',
        //                 "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire->getImageBlob().'"/></p><p>'.$partenaire->getDescription().'</p><p><b>Le service collecte:</b><br/>'.$partenaire->getCollecte().'</p><p>'.$detailsVente.'</p>',
        //                 "url" => $partenaire->getUrl(),
        //                 "size" => "35",
        //                 "image_url" => "imgURL"
        //             ]);
        //         }
        //     }
        // }


        // $object = (object)array();
        // foreach ($depots as $keys => $value ) {
        //     $object->{$keys} = $value;
        // }
        // $jsonStructure = json_encode($object); 

        // return $this->render('site/partenaire/'.$pays.'/carte-'.$pays.'.html.twig', [
        //     'partenaires' => $partenaires,
        //     'depots' => $jsonStructure,
        //     'informationsLegales' =>  $this->informationsLegalesRepository->findAll(),
        //     'panier' => $this->panierRepository->findBy(['user' => $this->security->getUser(), 'etat' => 'panier'])
        // ]);
    }
}
