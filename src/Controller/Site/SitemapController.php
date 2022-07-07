<?php

namespace App\Controller\Site;

use App\Repository\BoiteRepository;
use App\Repository\OccasionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request, OccasionRepository $occasionRepository, BoiteRepository $boiteRepository): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $boites = $boiteRepository->findBy(['isOnLine' => true]);
        $occasions = $occasionRepository->findBy(['isOnLine' => true]);
        
        //tableau vide
        $urls = [];

        //liste des urls directes à completer
        $urls[] = [
                'loc'        => $this->generateUrl('accueil'),
                'changefreq' => "monthly", //monthly,daily
                'priority'   => 0.8
                ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_panier'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('cgv'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('mentions-legales'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('nous-soutenir'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('contact'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('app_register'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];
        $urls[] = [
            'loc'        => $this->generateUrl('contact'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];

        //liste des urls du catalogue pieces détachées
        $urls[] = [
                'loc'        => $this->generateUrl('catalogue_pieces_detachees'),
                'changefreq' => "monthly",
                'priority'   => 0.8
                ];
        foreach($boites as $boite){
            $urls[] = [
                'loc'     => $this->generateUrl('catalogue_pieces_detachees_demande', ['id' => $boite->getId(), 'slug' => $boite->getSlug(), 'editeur' => $this->slugger->slug($boite->getEditeur() ?? "VIDE")]),
                'lastmod' => $boite->getCreatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
			    'priority' => 0.8
            ];
        }

        //liste des urls du catalogue oocasions
        $urls[] = [
            'loc'        => $this->generateUrl('catalogue_jeux_occasion'),
            'changefreq' => "monthly",
            'priority'   => 0.8
            ];
        foreach($occasions as $occasion){
            $urls[] = [
                'loc'     => $this->generateUrl('catalogue_jeux_occasion_details', ['id' => $occasion->getId(), 'slug' => $occasion->getBoite()->getSlug(), 'editeur' => $this->slugger->slug($occasion->getBoite()->getEditeur() ?? "VIDE")]),
                'lastmod' => $occasion->getBoite()->getCreatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
                'priority' => 0.8
            ];
        }

        $response = new Response(
            $this->renderView('site/sitemap/index.html.twig', [
                'urls'     => $urls,
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');
        
        return $response;
    }
}
