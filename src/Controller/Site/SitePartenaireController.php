<?php

namespace App\Controller\Site;

use stdClass;
use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PaysRepository;
use App\Entity\InformationsLegales;
use App\Repository\DepartementRepository;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\InformationsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SitePartenaireController extends AbstractController
{
    /**
     * @Route("/partenaires/{pays}", name="carte_partenaires", methods={"GET"})
     */
    public function index(
        DepartementRepository $departementRepository,
        $pays,
        InformationsLegalesRepository $informationsLegalesRepository,
        PaysRepository $paysRepository,
        PartenaireRepository $partenaireRepository
        ): Response
    {

        $country = $paysRepository->findOneBy(['name' => $pays]);
        if(!$country) {
            $this->addFlash('danger', 'Pays inconnu!');
            return $this->redirectToRoute('accueil');
        }

        //on cherche les partenaires du pays selectionner
        $partenaires = $partenaireRepository->findBy(['country' => $country->getId(), 'isOnLine' => true]);

        $depots = [];
        
        //on boucle sur chaque partenaire
        foreach($partenaires as $key => $partenaire){

            //affichage details des ventes
            if($partenaire->getIsComplet() == 1 || $partenaire->getIsDetachee() == 1){
                $detailsVente= "<b>Le service vend:</b><br/>".$partenaire->getVend();
            }else{
                $detailsVente = "";
            }

            if($partenaire->getId() == 10 || $partenaire->getId() == 14){

                if($partenaire->getId() == 14){
                $size = 45; //35 commun
                }else{
                $size = 55; //55 pour logo caen
                }

                array_push($depots,
                [
                "lat" => $partenaire->getVille()->getLat(),
                "lng" => $partenaire->getVille()->getLng(),
                "name" => $partenaire->getName().' à '.$partenaire->getVille()->getVilleNom().' ('.$partenaire->getVille()->getVilleDepartement().')',
                "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire->getImageBlob().'"/></p><p>'.$partenaire->getDescription().'</p><p><b>Le service collecte:</b><br/>'.$partenaire->getCollecte().'</p><p>'.$detailsVente.'</p>',
                "url" => $partenaire->getUrl(),
                "type" => "image",
                "image_url" => "https://www.refaitesvosjeux.fr/images/design/logoDepots.png",
                "size" => $size,
                "image_url" => "imgURL"
                ]);
            }else{
                if($partenaire->getIsEcommerce() == 1){
                    array_push($depots,
                    [
                        "lat" => $partenaire->getVille()->getLat(),
                        "lng" => $partenaire->getVille()->getLng(),
                        "name" => $partenaire->getName().' à '.$partenaire->getVille()->getVilleNom().' ('.$partenaire->getVille()->getVilleDepartement().')',
                        "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire->getImageBlob().'"/></p><p>'.$partenaire->getDescription().'</p><p><b>Le service collecte:</b><br/>'.$partenaire->getCollecte().'</p><p>'.$detailsVente.'</p><p>Site E-commerce disponible.</p>',
                        "url" => $partenaire->getUrl(),
                        "size" => "35",
                        "image_url" => "imgURL"
                    ]);
                }else{
                    array_push($depots,
                    [
                        "lat" => $partenaire->getVille()->getLat(),
                        "lng" => $partenaire->getVille()->getLng(),
                        "name" => $partenaire->getName().' à '.$partenaire->getVille()->getVilleNom().' ('.$partenaire->getVille()->getVilleDepartement().')',
                        "description" => '<p style="margin-top:10px; width:100%; text-align:center;"><img style="width:100px;" src="data:image/jpeg;base64,'.$partenaire->getImageBlob().'"/></p><p>'.$partenaire->getDescription().'</p><p><b>Le service collecte:</b><br/>'.$partenaire->getCollecte().'</p><p>'.$detailsVente.'</p>',
                        "url" => $partenaire->getUrl(),
                        "size" => "35",
                        "image_url" => "imgURL"
                    ]);
                }
            }
        }


        $object = (object)array();
        foreach ($depots as $keys => $value ) {
            $object->{$keys} = $value;
        }
        $jsonStructure = json_encode($object); 

        return $this->render('site/partenaire/'.$pays.'/carte-'.$pays.'.html.twig', [
            'partenaires' => $partenaires,
            'depots' => $jsonStructure,
            'informationsLegales' =>  $informationsLegalesRepository->findAll()
        ]);
    }

    // /**
    //  * @Route("/partenaires/{pays}", name="carte_partenaires", methods={"GET"})
    //  */
    // public function index(
    //     PartenaireRepository $partenaireRepository,
    //     $pays,
    //     InformationsLegalesRepository $informationsLegalesRepository,
    //     PaysRepository $paysRepository
    // ): Response {

    //     $country = $paysRepository->findOneBy(['name' => $pays]);

    //     if (!$country) {
    //         $this->addFlash('danger', 'Pays inconnu!');
    //         return $this->redirectToRoute('accueil');
    //     }

    //     $partenaires = $partenaireRepository->findBy(['country' => $country, 'isOnLine' => true], ['name' => 'ASC']);

    //     $images = [];
    //     foreach ($partenaires as $key => $partenaire) {

    //         $images[$key] = stream_get_contents($partenaire->getImageBlob());
    //     }


    //     return $this->render('site/partenaire/partenaires-' . $country->getName() . '.html.twig', [
    //         'partenaires' => $partenaires,
    //         'informationsLegales' =>  $informationsLegalesRepository->findAll(),
    //         'images' => $images,
    //         'country' => $country
    //     ]);
    // }
}
