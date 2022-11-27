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
}
