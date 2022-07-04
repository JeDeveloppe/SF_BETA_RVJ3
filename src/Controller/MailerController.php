<?php

namespace App\Controller;

use DateInterval;
use DateTimeImmutable;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConfigurationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerController extends AbstractController
{
    /**
     * @Route("/email/mise-a-disposition-devis/{token}", name="mailer_send_mise_a_disposition_devis")
     */
    public function sendEmailDevisMisAdisposition(
        DocumentRepository $documentRepository,
        ConfigurationRepository $configurationRepository,
        Request $request,
        MailerInterface $mailer,
        $token
        ): Response
    {

        $compteSmtp = $this->getParameter('COMPTESMTP');
        $config = $configurationRepository->findAll();

        $document = $documentRepository->findOneBy(['token' => $token]);

        $host = $request->getSchemeAndHttpHost();
        $link = $request->getSchemeAndHttpHost().$this->generateUrl('lecture_devis_avant_paiement', ['token' => $token]);
  
        $email = (new TemplatedEmail())
            ->from($compteSmtp)
            ->to($document->getUser()->getEmail())
            ->replyTo($compteSmtp)
            ->subject("Devis ".$config[0]->getPrefixeDevis().$document->getNumeroDevis()." disponible jusqu'au ".$document->getEndValidationDevis()->format('d-m-Y'))
            ->htmlTemplate('email/devis_disponible.html.twig')
            ->context([
                'link' => $link,
                'document' => $document,
                'host' => $host
            ]);
        

        $mailer->send($email);

        $this->addFlash('success', 'Email envoyé avec succès!');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/email/relance-devis/{token}/", name="mailer_relance_devis")
     */
    public function relanceDevisDeXJours(
        $token,
        DocumentRepository $documentRepository,
        EntityManagerInterface $em,
        Request $request,
        ConfigurationRepository $configurationRepository,
        MailerInterface $mailer
        ): Response
        {

        $compteSmtp = $this->getParameter('COMPTESMTP');
        $configurations = $configurationRepository->findAll();

        if(count($configurations) < 1){
            $delaiDevis = 2; //on relance de 2 jours minimum par defaut
        }else{
            $delaiDevis = $configurations[0]->getDevisDelayBeforeDelete();
        }

        //on cherche le devis par le token
        $devis = $documentRepository->findOneBy(['token' => $token]);

        //on recupere la date du jour et on ajoute X jours
        $now = new DateTimeImmutable();
        $endDevis = $now->add(new DateInterval('P'.$delaiDevis.'D'));
        
        $devis->setEndValidationDevis($endDevis);

        $em->merge($devis);
        $em->flush();
       
        $host = $request->getSchemeAndHttpHost();
        $link = $request->getSchemeAndHttpHost().$this->generateUrl('lecture_devis_avant_paiement', ['token' => $token]);
  

        $email = (new TemplatedEmail())
            ->from($compteSmtp)
            ->to($devis->getUser()->getEmail())
            ->replyTo($compteSmtp)
            ->subject("Rappel devis ".$devis->getNumeroDevis()." disponible jusqu'au ".$devis->getEndValidationDevis()->format('d-m-Y'))
            ->htmlTemplate('email/relance_devis.html.twig')
            ->context([
                'link' => $link,
                'document' => $devis,
                'host' => $host
            ]);
        

        $mailer->send($email);

        //on signal le changement
        $this->addFlash('success', 'Devis relancer de '.$delaiDevis.' jours!');
        return $this->redirect($request->headers->get('referer'));
    }
}