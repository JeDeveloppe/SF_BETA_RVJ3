<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener{

    private $maintenance;

    public function __construct(
        string $maintenance,
        private Environment $twig
        )
    {
        $this->maintenance = $maintenance;
    }

    public function onKernelRequest(RequestEvent $event){
            
        //on vérifie si le fichier existe
        if(!file_exists($this->maintenance)){
            return;
        }

        $event->setResponse(
                new Response($this->twig->render('site/maintenance/index.html.twig'), Response::HTTP_SERVICE_UNAVAILABLE
                )
            );
            $event->stopPropagation();
    }
}