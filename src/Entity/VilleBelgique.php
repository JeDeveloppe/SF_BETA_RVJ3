<?php

namespace App\Entity;

use App\Repository\VilleBelgiqueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleBelgiqueRepository::class)
 */
class VilleBelgique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $villeCodePostal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $villeNom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lng;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $villeProvince;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleCodePostal(): ?string
    {
        return $this->villeCodePostal;
    }

    public function setVilleCodePostal(string $villeCodePostal): self
    {
        $this->villeCodePostal = $villeCodePostal;

        return $this;
    }

    public function getVilleNom(): ?string
    {
        return $this->villeNom;
    }

    public function setVilleNom(string $villeNom): self
    {
        $this->villeNom = $villeNom;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getVilleProvince(): ?string
    {
        return $this->villeProvince;
    }

    public function setVilleProvince(string $villeProvince): self
    {
        $this->villeProvince = $villeProvince;

        return $this;
    }
}
