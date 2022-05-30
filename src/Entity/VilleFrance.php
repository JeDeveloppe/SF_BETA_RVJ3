<?php

namespace App\Entity;

use App\Repository\VilleFranceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleFranceRepository::class)
 */
class VilleFrance
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
    private $villeDepartement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lng;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lat;

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

    public function getVilleDepartement(): ?string
    {
        return $this->villeDepartement;
    }

    public function setVilleDepartement(string $villeDepartement): self
    {
        $this->villeDepartement = $villeDepartement;

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
}
