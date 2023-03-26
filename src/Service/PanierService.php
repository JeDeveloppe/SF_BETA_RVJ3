<?php

namespace App\Service;

use App\Repository\DeliveryRepository;

class PanierService
{
    public function __construct(
        private DeliveryRepository $deliveryRepository
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
            $total += $ligne->getArticle()->getPriceHt() * $ligne->getArticleQuantity();
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
}