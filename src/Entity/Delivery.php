<?php

namespace App\Entity;

use App\Repository\DeliveryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryRepository::class)
 */
class Delivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $weightStart;

    /**
     * @ORM\Column(type="integer")
     */
    private $weightStop;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceHt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeightStart(): ?int
    {
        return $this->weightStart;
    }

    public function setWeightStart(int $weightStart): self
    {
        $this->weightStart = $weightStart;

        return $this;
    }

    public function getWeightStop(): ?int
    {
        return $this->weightStop;
    }

    public function setWeightStop(int $weightStop): self
    {
        $this->weightStop = $weightStop;

        return $this;
    }

    public function getPriceHt(): ?int
    {
        return $this->priceHt;
    }

    public function setPriceHt(int $priceHt): self
    {
        $this->priceHt = $priceHt;

        return $this;
    }
}
