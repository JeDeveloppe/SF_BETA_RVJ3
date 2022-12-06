<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaiementRepository::class)
 */
class Paiement
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
    private $tokenTransaction;

    /**
     * @ORM\Column(type="datetime_immutable",  nullable=true)
     */
    private $timeTransaction;

    /**
     * @ORM\Column(type="string", length=50,  nullable=true)
     */
    private $moyenPaiement;

    /**
     * @ORM\OneToOne(targetEntity=Document::class, mappedBy="paiement", cascade={"persist", "remove"})
     */
    private $document;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenTransaction(): ?string
    {
        return $this->tokenTransaction;
    }

    public function setTokenTransaction(string $tokenTransaction): self
    {
        $this->tokenTransaction = $tokenTransaction;

        return $this;
    }

    public function getTimeTransaction(): ?\DateTimeImmutable
    {
        return $this->timeTransaction;
    }

    public function setTimeTransaction(\DateTimeImmutable $timeTransaction): self
    {
        $this->timeTransaction = $timeTransaction;

        return $this;
    }

    public function getMoyenPaiement(): ?string
    {
        return $this->moyenPaiement;
    }

    public function setMoyenPaiement(string $moyenPaiement): self
    {
        $this->moyenPaiement = $moyenPaiement;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        // unset the owning side of the relation if necessary
        if ($document === null && $this->document !== null) {
            $this->document->setPaiement(null);
        }

        // set the owning side of the relation if necessary
        if ($document !== null && $document->getPaiement() !== $this) {
            $document->setPaiement($this);
        }

        $this->document = $document;

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



}
