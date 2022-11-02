<?php

namespace App\Entity;

use App\Repository\OccasionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OccasionRepository::class)
 */
class Occasion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $priceHt;

    /**
     * @ORM\Column(type="string", length=210)
     */
    private $oldPriceHt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $information;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isNeuf;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etatBoite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etatMateriel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $regleJeu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOnLine;

    /**
     * @ORM\ManyToOne(targetEntity=Boite::class, inversedBy="occasions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDonation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSale;

    /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="occasion")
     */
    private $paniers;

    /**
     * @ORM\OneToMany(targetEntity=DocumentLignes::class, mappedBy="occasion")
     */
    private $documentLignes;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $donation;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $sale;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $meansOfSale;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $prixDeVente;

    public function __construct()
    {
        $this->paniers = new ArrayCollection();
        $this->documentLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPriceHt(): ?string
    {
        return $this->priceHt;
    }

    public function setPriceHt(string $priceHt): self
    {
        $this->priceHt = $priceHt;

        return $this;
    }

    public function getOldPriceHt(): ?string
    {
        return $this->oldPriceHt;
    }

    public function setOldPriceHt(?string $oldPriceHt): self
    {
        $this->oldPriceHt = $oldPriceHt;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getIsNeuf(): ?bool
    {
        return $this->isNeuf;
    }

    public function setIsNeuf(bool $isNeuf): self
    {
        $this->isNeuf = $isNeuf;

        return $this;
    }

    public function getEtatBoite(): ?string
    {
        return $this->etatBoite;
    }

    public function setEtatBoite(string $etatBoite): self
    {
        $this->etatBoite = $etatBoite;

        return $this;
    }

    public function getEtatMateriel(): ?string
    {
        return $this->etatMateriel;
    }

    public function setEtatMateriel(string $etatMateriel): self
    {
        $this->etatMateriel = $etatMateriel;

        return $this;
    }

    public function getRegleJeu(): ?string
    {
        return $this->regleJeu;
    }

    public function setRegleJeu(string $regleJeu): self
    {
        $this->regleJeu = $regleJeu;

        return $this;
    }

    public function getIsOnLine(): ?bool
    {
        return $this->isOnLine;
    }

    public function setIsOnLine(bool $isOnLine): self
    {
        $this->isOnLine = $isOnLine;

        return $this;
    }

    public function getBoite(): ?Boite
    {
        return $this->boite;
    }

    public function setBoite(?Boite $boite): self
    {
        $this->boite = $boite;

        return $this;
    }

    public function getIsDonation(): ?bool
    {
        return $this->isDonation;
    }

    public function setIsDonation(?bool $isDonation): self
    {
        $this->isDonation = $isDonation;

        return $this;
    }

    public function getIsSale(): ?bool
    {
        return $this->isSale;
    }

    public function setIsSale(?bool $isSale): self
    {
        $this->isSale = $isSale;

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setOccasion($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getOccasion() === $this) {
                $panier->setOccasion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DocumentLignes>
     */
    public function getDocumentLignes(): Collection
    {
        return $this->documentLignes;
    }

    public function addDocumentLigne(DocumentLignes $documentLigne): self
    {
        if (!$this->documentLignes->contains($documentLigne)) {
            $this->documentLignes[] = $documentLigne;
            $documentLigne->setOccasion($this);
        }

        return $this;
    }

    public function removeDocumentLigne(DocumentLignes $documentLigne): self
    {
        if ($this->documentLignes->removeElement($documentLigne)) {
            // set the owning side to null (unless already changed)
            if ($documentLigne->getOccasion() === $this) {
                $documentLigne->setOccasion(null);
            }
        }

        return $this;
    }

    public function getDonation(): ?\DateTimeImmutable
    {
        return $this->donation;
    }

    public function setDonation(?\DateTimeImmutable $donation): self
    {
        $this->donation = $donation;

        return $this;
    }

    public function getSale(): ?\DateTimeImmutable
    {
        return $this->sale;
    }

    public function setSale(?\DateTimeImmutable $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    public function getMeansOfSale(): ?string
    {
        return $this->meansOfSale;
    }

    public function setMeansOfSale(?string $meansOfSale): self
    {
        $this->meansOfSale = $meansOfSale;

        return $this;
    }

    public function getStock(): ?string
    {
        return $this->stock;
    }

    public function setStock(string $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPrixDeVente(): ?string
    {
        return $this->prixDeVente;
    }

    public function setPrixDeVente(?string $prixDeVente): self
    {
        $this->prixDeVente = $prixDeVente;

        return $this;
    }
}
