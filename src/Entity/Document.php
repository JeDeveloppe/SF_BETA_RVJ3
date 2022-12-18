<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $totalTTC;

    /**
     * @ORM\Column(type="float")
     */
    private $totalHT;

    /**
     * @ORM\Column(type="float")
     */
    private $totalLivraison;

    /**
     * @ORM\Column(type="integer")
     */
    private $numeroDevis;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numeroFacture;

    /**
     * @ORM\Column(type="text")
     */
    private $adresseFacturation;

    /**
     * @ORM\Column(type="text")
     */
    private $adresseLivraison;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endValidationDevis;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRelanceDevis;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $envoiEmailDevis;

    /**
     * @ORM\OneToMany(targetEntity=DocumentLignes::class, mappedBy="document")
     */
    private $documentLignes;

    /**
     * @ORM\Column(type="integer")
     */
    private $tauxTva;

    /**
     * @ORM\ManyToOne(targetEntity=MethodeEnvoi::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $envoi;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleteByUser;

    /**
     * @ORM\OneToOne(targetEntity=Paiement::class, inversedBy="document", cascade={"persist", "remove"})
     */
    private $paiement;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rvj2Id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cost;

    /**
     * @ORM\OneToMany(targetEntity=Occasion::class, mappedBy="document")
     */
    private $occasions;



    
    public function __construct()
    {
        $this->documentLignes = new ArrayCollection();
        $this->occasions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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

    public function getTotalTTC(): ?float
    {
        return $this->totalTTC;
    }

    public function setTotalTTC(float $totalTTC): self
    {
        $this->totalTTC = $totalTTC;

        return $this;
    }

    public function getTotalHT(): ?float
    {
        return $this->totalHT;
    }

    public function setTotalHT(float $totalHT): self
    {
        $this->totalHT = $totalHT;

        return $this;
    }

    public function getTotalLivraison(): ?float
    {
        return $this->totalLivraison;
    }

    public function setTotalLivraison(float $totalLivraison): self
    {
        $this->totalLivraison = $totalLivraison;

        return $this;
    }

    public function getNumeroDevis(): ?int
    {
        return $this->numeroDevis;
    }

    public function setNumeroDevis(?int $numeroDevis): self
    {
        $this->numeroDevis = $numeroDevis;

        return $this;
    }

    public function getNumeroFacture(): ?int
    {
        return $this->numeroFacture;
    }

    public function setNumeroFacture(?int $numeroFacture): self
    {
        $this->numeroFacture = $numeroFacture;

        return $this;
    }

    public function getAdresseFacturation(): ?string
    {
        return $this->adresseFacturation;
    }

    public function setAdresseFacturation(string $adresseFacturation): self
    {
        $this->adresseFacturation = $adresseFacturation;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): self
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getEndValidationDevis(): ?\DateTimeImmutable
    {
        return $this->endValidationDevis;
    }

    public function setEndValidationDevis(\DateTimeImmutable $endValidationDevis): self
    {
        $this->endValidationDevis = $endValidationDevis;

        return $this;
    }

    public function getIsRelanceDevis(): ?bool
    {
        return $this->isRelanceDevis;
    }

    public function setIsRelanceDevis(bool $isRelanceDevis): self
    {
        $this->isRelanceDevis = $isRelanceDevis;

        return $this;
    }

    public function getEnvoiEmailDevis(): ?\DateTimeImmutable
    {
        return $this->envoiEmailDevis;
    }

    public function setEnvoiEmailDevis(?\DateTimeImmutable $envoiEmailDevis): self
    {
        $this->envoiEmailDevis = $envoiEmailDevis;

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
            $documentLigne->setDocument($this);
        }

        return $this;
    }

    public function removeDocumentLigne(DocumentLignes $documentLigne): self
    {
        if ($this->documentLignes->removeElement($documentLigne)) {
            // set the owning side to null (unless already changed)
            if ($documentLigne->getDocument() === $this) {
                $documentLigne->setDocument(null);
            }
        }

        return $this;
    }

    public function getTauxTva(): ?int
    {
        return $this->tauxTva;
    }

    public function setTauxTva(int $tauxTva): self
    {
        $this->tauxTva = $tauxTva;

        return $this;
    }

    public function getEnvoi(): ?MethodeEnvoi
    {
        return $this->envoi;
    }

    public function setEnvoi(?MethodeEnvoi $envoi): self
    {
        $this->envoi = $envoi;

        return $this;
    }

    public function getIsDeleteByUser(): ?bool
    {
        return $this->isDeleteByUser;
    }

    public function setIsDeleteByUser(?bool $isDeleteByUser): self
    {
        $this->isDeleteByUser = $isDeleteByUser;

        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): self
    {
        $this->paiement = $paiement;

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

    public function getRvj2Id(): ?int
    {
        return $this->rvj2Id;
    }

    public function setRvj2Id(?int $rvj2Id): self
    {
        $this->rvj2Id = $rvj2Id;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(?int $cost): self
    {
        $this->cost = $cost;

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
            $occasion->setDocument($this);
        }

        return $this;
    }

    public function removeOccasion(Occasion $occasion): self
    {
        if ($this->occasions->removeElement($occasion)) {
            // set the owning side to null (unless already changed)
            if ($occasion->getDocument() === $this) {
                $occasion->setDocument(null);
            }
        }

        return $this;
    }



}
