<?php

namespace App\Entity;

use App\Repository\DocumentLignesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentLignesRepository::class)
 */
class DocumentLignes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Boite::class, inversedBy="documentLignes")
     */
    private $boite;

    /**
     * @ORM\ManyToOne(targetEntity=Occasion::class, inversedBy="documentLignes")
     */
    private $occasion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="float")
     */
    private $prixVente;

    /**
     * @ORM\ManyToOne(targetEntity=Document::class, inversedBy="documentLignes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reponse;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOccasion(): ?Occasion
    {
        return $this->occasion;
    }

    public function setOccasion(?Occasion $occasion): self
    {
        $this->occasion = $occasion;

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

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(float $prixVente): self
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

}
