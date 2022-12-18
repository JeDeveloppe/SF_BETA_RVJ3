<?php

namespace App\Entity;

use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ConfigurationRepository::class)
 */
class Configuration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive
     * @Assert\Range(
     *      min = 3,
     *      max = 10,
     *      notInRangeMessage = "Doit être entre {{ min }} et {{ max }}",
     * )
     */
    private $DevisDelayBeforeDelete;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\Length(
     *      min = 2,
     *      max = 5,
     *      minMessage = "Minimum {{ limit }} charactères",
     *      maxMessage = "Maximum {{ limit }} charactères"
     * )
     */
    private $prefixeFacture;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\Length(
     *      min = 2,
     *      max = 5,
     *      minMessage = "Minimum {{ limit }} charactères",
     *      maxMessage = "Maximum {{ limit }} charactères"
     * )
     */
    private $prefixeDevis;

    /**
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $grandPlateau_bois;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $grandPlateau_plastique;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $petitPlateau_bois;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $petitPlateau_plastique;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pieceUnique;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pieceMultiple;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pieceMetalBois;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $autrePiece;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $enveloppe_simple;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $enveloppe_bulle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $holiday;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevisDelayBeforeDelete(): ?float
    {
        return $this->DevisDelayBeforeDelete;
    }

    public function setDevisDelayBeforeDelete(float $DevisDelayBeforeDelete): self
    {
        $this->DevisDelayBeforeDelete = $DevisDelayBeforeDelete;

        return $this;
    }

    public function getPrefixeFacture(): ?string
    {
        return $this->prefixeFacture;
    }

    public function setPrefixeFacture(string $prefixeFacture): self
    {
        $this->prefixeFacture = $prefixeFacture;

        return $this;
    }

    public function getPrefixeDevis(): ?string
    {
        return $this->prefixeDevis;
    }

    public function setPrefixeDevis(string $prefixeDevis): self
    {
        $this->prefixeDevis = $prefixeDevis;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getGrandPlateauBois(): ?string
    {
        return $this->grandPlateau_bois;
    }

    public function setGrandPlateauBois(string $grandPlateau_bois): self
    {
        $this->grandPlateau_bois = $grandPlateau_bois;

        return $this;
    }

    public function getGrandPlateauPlastique(): ?string
    {
        return $this->grandPlateau_plastique;
    }

    public function setGrandPlateauPlastique(string $grandPlateau_plastique): self
    {
        $this->grandPlateau_plastique = $grandPlateau_plastique;

        return $this;
    }

    public function getPetitPlateauBois(): ?string
    {
        return $this->petitPlateau_bois;
    }

    public function setPetitPlateauBois(string $petitPlateau_bois): self
    {
        $this->petitPlateau_bois = $petitPlateau_bois;

        return $this;
    }

    public function getPetitPlateauPlastique(): ?string
    {
        return $this->petitPlateau_plastique;
    }

    public function setPetitPlateauPlastique(string $petitPlateau_plastique): self
    {
        $this->petitPlateau_plastique = $petitPlateau_plastique;

        return $this;
    }

    public function getPieceUnique(): ?string
    {
        return $this->pieceUnique;
    }

    public function setPieceUnique(string $pieceUnique): self
    {
        $this->pieceUnique = $pieceUnique;

        return $this;
    }

    public function getPieceMultiple(): ?string
    {
        return $this->pieceMultiple;
    }

    public function setPieceMultiple(string $pieceMultiple): self
    {
        $this->pieceMultiple = $pieceMultiple;

        return $this;
    }

    public function getPieceMetalBois(): ?string
    {
        return $this->pieceMetalBois;
    }

    public function setPieceMetalBois(string $pieceMetalBois): self
    {
        $this->pieceMetalBois = $pieceMetalBois;

        return $this;
    }

    public function getAutrePiece(): ?string
    {
        return $this->autrePiece;
    }

    public function setAutrePiece(string $autrePiece): self
    {
        $this->autrePiece = $autrePiece;

        return $this;
    }

    public function getEnveloppeSimple(): ?string
    {
        return $this->enveloppe_simple;
    }

    public function setEnveloppeSimple(string $enveloppe_simple): self
    {
        $this->enveloppe_simple = $enveloppe_simple;

        return $this;
    }

    public function getEnveloppeBulle(): ?string
    {
        return $this->enveloppe_bulle;
    }

    public function setEnveloppeBulle(string $enveloppe_bulle): self
    {
        $this->enveloppe_bulle = $enveloppe_bulle;

        return $this;
    }

    public function getHoliday(): ?string
    {
        return $this->holiday;
    }

    public function setHoliday(?string $holiday): self
    {
        $this->holiday = $holiday;

        return $this;
    }
}
