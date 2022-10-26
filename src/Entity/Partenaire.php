<?php

namespace App\Entity;

use App\Repository\PartenaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PartenaireRepository::class)
 */
class Partenaire
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $collecte;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $vend;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="blob")
     */
    private $imageBlob;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDetachee;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isComplet;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEcommerce;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isOnLine;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="partenaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="partenaires")
     * @Assert\NotBlank
     */
    private $ville;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCollecte(): ?string
    {
        return $this->collecte;
    }

    public function setCollecte(?string $collecte): self
    {
        $this->collecte = $collecte;

        return $this;
    }

    public function getVend(): ?string
    {
        return $this->vend;
    }

    public function setVend(?string $vend): self
    {
        $this->vend = $vend;

        return $this;
    }

    public function getIsDon(): ?bool
    {
        return $this->isDon;
    }

    public function setIsDon(?bool $isDon): self
    {
        $this->isDon = $isDon;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getImageBlob()
    {
        // return $this->imageBlob;
        if(!is_null($this->imageBlob)){
            return stream_get_contents($this->imageBlob,-1,0);
        }
    }

    public function setImageBlob($imageBlob): self
    {
        $this->imageBlob = $imageBlob;

        return $this;
    }

    public function getIsDetachee(): ?bool
    {
        return $this->isDetachee;
    }

    public function setIsDetachee(?bool $isDetachee): self
    {
        $this->isDetachee = $isDetachee;

        return $this;
    }

    public function getIsComplet(): ?bool
    {
        return $this->isComplet;
    }

    public function setIsComplet(?bool $isComplet): self
    {
        $this->isComplet = $isComplet;

        return $this;
    }

    public function getIsEcommerce(): ?bool
    {
        return $this->isEcommerce;
    }

    public function setIsEcommerce(?bool $isEcommerce): self
    {
        $this->isEcommerce = $isEcommerce;

        return $this;
    }

    public function getIsOnLine(): ?bool
    {
        return $this->isOnLine;
    }

    public function setIsOnLine(?bool $isOnLine): self
    {
        $this->isOnLine = $isOnLine;

        return $this;
    }

    public function getCountry(): ?Pays
    {
        return $this->country;
    }

    public function setCountry(?Pays $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}
