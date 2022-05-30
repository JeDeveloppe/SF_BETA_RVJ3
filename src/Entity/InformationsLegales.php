<?php

namespace App\Entity;

use App\Repository\InformationsLegalesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InformationsLegalesRepository::class)
 */
class InformationsLegales
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresseSociete;

    /**
     * @ORM\Column(type="integer")
     */
    private $siretSociete;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresseMailSite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $societeWebmaster;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomWebmaster;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomSociete;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $siteUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hebergeurSite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresseSociete(): ?string
    {
        return $this->adresseSociete;
    }

    public function setAdresseSociete(string $adresseSociete): self
    {
        $this->adresseSociete = $adresseSociete;

        return $this;
    }

    public function getSiretSociete(): ?int
    {
        return $this->siretSociete;
    }

    public function setSiretSociete(int $siretSociete): self
    {
        $this->siretSociete = $siretSociete;

        return $this;
    }

    public function getAdresseMailSite(): ?string
    {
        return $this->adresseMailSite;
    }

    public function setAdresseMailSite(string $adresseMailSite): self
    {
        $this->adresseMailSite = $adresseMailSite;

        return $this;
    }

    public function getSocieteWebmaster(): ?string
    {
        return $this->societeWebmaster;
    }

    public function setSocieteWebmaster(string $societeWebmaster): self
    {
        $this->societeWebmaster = $societeWebmaster;

        return $this;
    }

    public function getNomWebmaster(): ?string
    {
        return $this->nomWebmaster;
    }

    public function setNomWebmaster(string $nomWebmaster): self
    {
        $this->nomWebmaster = $nomWebmaster;

        return $this;
    }

    public function getNomSociete(): ?string
    {
        return $this->nomSociete;
    }

    public function setNomSociete(string $nomSociete): self
    {
        $this->nomSociete = $nomSociete;

        return $this;
    }

    public function getSiteUrl(): ?string
    {
        return $this->siteUrl;
    }

    public function setSiteUrl(string $siteUrl): self
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function getHebergeurSite(): ?string
    {
        return $this->hebergeurSite;
    }

    public function setHebergeurSite(string $hebergeurSite): self
    {
        $this->hebergeurSite = $hebergeurSite;

        return $this;
    }
}
