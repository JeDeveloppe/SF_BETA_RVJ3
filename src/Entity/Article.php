<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
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
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\ManyToMany(targetEntity=Boite::class, inversedBy="articles")
     */
    private $boite;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Boite::class, inversedBy="articlesOrigine")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boiteOrigine;

    /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="article")
     */
    private $paniers;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceHt;

    /**
     * @ORM\OneToMany(targetEntity=DocumentLignes::class, mappedBy="article")
     */
    private $documentLignes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight;

    /**
     * @ORM\ManyToMany(targetEntity=Boite::class, inversedBy="articlesRelative")
     * @ORM\JoinTable(name="articleBoiteRelative")
     */
    private $BoiteRelative;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Envelope::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Envelope;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Dimension;

    public function __construct()
    {
        $this->boite = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->documentLignes = new ArrayCollection();
        $this->BoiteRelative = new ArrayCollection();
    }

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
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

    /**
     * @return Collection<int, Boite>
     */
    public function getBoite(): Collection
    {
        return $this->boite;
    }

    public function addBoite(Boite $boite): self
    {
        if (!$this->boite->contains($boite)) {
            $this->boite[] = $boite;
        }

        return $this;
    }

    public function removeBoite(Boite $boite): self
    {
        $this->boite->removeElement($boite);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBoiteOrigine(): ?Boite
    {
        return $this->boiteOrigine;
    }

    public function setBoiteOrigine(?Boite $boiteOrigine): self
    {
        $this->boiteOrigine = $boiteOrigine;

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
            $panier->setArticle($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getArticle() === $this) {
                $panier->setArticle(null);
            }
        }

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
            $documentLigne->setArticle($this);
        }

        return $this;
    }

    public function removeDocumentLigne(DocumentLignes $documentLigne): self
    {
        if ($this->documentLignes->removeElement($documentLigne)) {
            // set the owning side to null (unless already changed)
            if ($documentLigne->getArticle() === $this) {
                $documentLigne->setArticle(null);
            }
        }

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Collection<int, Boite>
     */
    public function getBoiteRelative(): Collection
    {
        return $this->BoiteRelative;
    }

    public function addBoiteRelative(Boite $boiteRelative): self
    {
        if (!$this->BoiteRelative->contains($boiteRelative)) {
            $this->BoiteRelative[] = $boiteRelative;
        }

        return $this;
    }

    public function removeBoiteRelative(Boite $boiteRelative): self
    {
        $this->BoiteRelative->removeElement($boiteRelative);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getEnvelope(): ?Envelope
    {
        return $this->Envelope;
    }

    public function setEnvelope(?Envelope $Envelope): self
    {
        $this->Envelope = $Envelope;

        return $this;
    }

    public function getDimension(): ?string
    {
        return $this->Dimension;
    }

    public function setDimension(?string $Dimension): self
    {
        $this->Dimension = $Dimension;

        return $this;
    }
}
