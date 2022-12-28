<?php

namespace App\Entity;

use App\Repository\PanierImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanierImageRepository::class)
 */
class PanierImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="blob")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Panier::class, inversedBy="panierImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $panier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage()
    {
        // return $this->image;
        return stream_get_contents($this->image,-1,0);
        
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }
}
