<?php

namespace App\Service;

use App\Repository\DeliveryRepository;
use App\Repository\EnvelopeRepository;

class PanierService
{
    public function __construct(
        private DeliveryRepository $deliveryRepository,
        private EnvelopeRepository $envelopeRepository
    )
    {
    }

    public function totalHtOfArticles($shoppingCartLignes){
        
        $total = 0;

        foreach($shoppingCartLignes as $ligne){
            $total += $ligne->getArticle()->getPriceHt() * $ligne->getArticleQuantity();
        }

        return $total;
    }

    public function totalWeightOfArticles($shoppingCartLignes){
        
        $total = 0;

        foreach($shoppingCartLignes as $ligne){
            $total += $ligne->getArticle()->getWeight() * $ligne->getArticleQuantity();
        }

        return $total;
    }

    public function totalHtOfOccasions($shoppingCartLignes){
        
        $total = 0;

        foreach($shoppingCartLignes as $ligne){
            $total += $ligne->getOccasion()->getPriceHt();

        }

        return $total;
    }

    public function totalWeightOfOccasions($shoppingCartLignes){
        
        $total = 0;

        foreach($shoppingCartLignes as $ligne){
            $total += $ligne->getOccasion()->getBoite()->getPoidBoite();

        }

        return $total;
    }

    public function getPriceFromWeight($totalWeight){

        $delivery = $this->deliveryRepository->findDelivery($totalWeight);
        
        return $delivery->getPriceHt();
    }

    public function weightEnvelopeFromBigestArticle($shoppingCartLignes){

        $envelopes = [];

        foreach($shoppingCartLignes as $ligne){
            $envelopes[] = $ligne->getArticle()->getEnvelope()->getId();
        }
        $envelopeMax = max($envelopes);

        $envelope = $this->envelopeRepository->findOneBy(['id' => $envelopeMax]);

        return $envelope;

    }
}