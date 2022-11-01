<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Boite
 *
 * @ORM\Table(name="boite")
 * @ORM\Entity
 */
class Boite
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=919, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="editeur", type="string", length=30, nullable=true)
     */
    private $editeur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="annee", type="string", length=20, nullable=true)
     */
    private $annee;

    /**
     * @var string
     *
     * @ORM\Column(name="imageBlob", type="blob", length=0, nullable=false)
     */
    private $imageblob;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_livrable", type="boolean", nullable=false)
     */
    private $isLivrable;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_complet", type="boolean", nullable=false)
     */
    private $isComplet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="poid_boite", type="string", length=255, nullable=true)
     */
    private $poidBoite;

    /**
     * @var int|null
     *
     * @ORM\Column(name="age", type="string", length=10, nullable=true)
     */
    private $age;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nbr_joueurs", type="string", length=4, nullable=true)
     */
    private $nbrJoueurs;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prix_ht", type="string", length=4, nullable=true)
     */
    private $prixHt;

    /**
     * @var string
     *
     * @ORM\Column(name="is_deee", type="boolean", nullable=true)
     */
    private $isDeee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contenu", type="text", length=65535, nullable=true)
     */
    private $contenu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_on_line", type="boolean", nullable=false)
     */
    private $isOnLine;

    /**
     * @var datetime_immutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @ORM\OneToMany(targetEntity=Occasion::class, mappedBy="boite", orphanRemoval=true)
     */
    private $occasions;

    /**
     * @ORM\OneToMany(targetEntity=Panier::class, mappedBy="boite")
     */
    private $paniers;

    /**
     * @ORM\OneToMany(targetEntity=DocumentLignes::class, mappedBy="boite")
     */
    private $documentLignes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $creator;

    public function __construct()
    {
        $this->occasions = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->documentLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(?string $editeur): self
    {
        $this->editeur = $editeur;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(?string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getImageblob()
    {
        //return $this->imageblob;
        if(!is_null($this->imageblob)){
            return stream_get_contents($this->imageblob,-1,0);
        }
    }

    public function setImageblob($imageblob): self
    {
        $this->imageblob = $imageblob;

        return $this;
        
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getIsLivrable(): ?bool
    {
        return $this->isLivrable;
    }

    public function setIsLivrable(bool $isLivrable): self
    {
        $this->isLivrable = $isLivrable;

        return $this;
    }

    public function getIsComplet(): ?bool
    {
        return $this->isComplet;
    }

    public function setIsComplet(bool $isComplet): self
    {
        $this->isComplet = $isComplet;

        return $this;
    }

    public function getPoidBoite(): ?string
    {
        return $this->poidBoite;
    }

    public function setPoidBoite(?string $poidBoite): self
    {
        $this->poidBoite = $poidBoite;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getNbrJoueurs(): ?string
    {
        return $this->nbrJoueurs;
    }

    public function setNbrJoueurs(?string $nbrJoueurs): self
    {
        $this->nbrJoueurs = $nbrJoueurs;

        return $this;
    }

    public function getPrixHt(): ?string
    {
        return $this->prixHt;
    }

    public function setPrixHt(?string $prixHt): self
    {
        $this->prixHt = $prixHt;

        return $this;
    }

    public function getIsDeee(): ?bool
    {
        return $this->isDeee;
    }

    public function setIsDeee(bool $isDeee): self
    {
        $this->isDeee = $isDeee;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Occasion>
     */
    public function getOccasions(): Collection
    {
        return $this->occasions;
    }

    public function addOccasion(Occasion $occasion): self
    {
        if (!$this->occasions->contains($occasion)) {
            $this->occasions[] = $occasion;
            $occasion->setBoite($this);
        }

        return $this;
    }

    public function removeOccasion(Occasion $occasion): self
    {
        if ($this->occasions->removeElement($occasion)) {
            // set the owning side to null (unless already changed)
            if ($occasion->getBoite() === $this) {
                $occasion->setBoite(null);
            }
        }

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
            $panier->setBoite($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getBoite() === $this) {
                $panier->setBoite(null);
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
            $documentLigne->setBoite($this);
        }

        return $this;
    }

    public function removeDocumentLigne(DocumentLignes $documentLigne): self
    {
        if ($this->documentLignes->removeElement($documentLigne)) {
            // set the owning side to null (unless already changed)
            if ($documentLigne->getBoite() === $this) {
                $documentLigne->setBoite(null);
            }
        }

        return $this;
    }

    public function getCreator(): ?string
    {
        return $this->creator;
    }

    public function setCreator(string $creator): self
    {
        $this->creator = $creator;

        return $this;
    }
}
