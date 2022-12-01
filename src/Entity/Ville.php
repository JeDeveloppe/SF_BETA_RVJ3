<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
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

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $pays;

    /**
     * @ORM\OneToMany(targetEntity=Adresse::class, mappedBy="ville")
     */
    private $adresses;

    /**
     * @ORM\OneToMany(targetEntity=Partenaire::class, mappedBy="ville")
     */
    private $partenaires;

    /**
     * @ORM\ManyToOne(targetEntity=Departement::class, inversedBy="villes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $departement;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rvj2Id;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->partenaires = new ArrayCollection();
    }

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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): self
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses[] = $adress;
            $adress->setVille($this);
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): self
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getVille() === $this) {
                $adress->setVille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Partenaire>
     */
    public function getPartenaires(): Collection
    {
        return $this->partenaires;
    }

    public function addPartenaire(Partenaire $partenaire): self
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires[] = $partenaire;
            $partenaire->setVille($this);
        }

        return $this;
    }

    public function removePartenaire(Partenaire $partenaire): self
    {
        if ($this->partenaires->removeElement($partenaire)) {
            // set the owning side to null (unless already changed)
            if ($partenaire->getVille() === $this) {
                $partenaire->setVille(null);
            }
        }

        return $this;
    }

    public function getDepartement(): ?Departement
    {
        return $this->departement;
    }

    public function setDepartement(?Departement $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getRvj2Id(): ?int
    {
        return $this->rvj2Id;
    }

    public function setRvj2Id(?int $rvj2Id): self
    {
        $this->rvj2Id = $rvj2Id;

        return $this;
    }
}
