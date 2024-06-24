<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CaisseRepository")
*/
class Caisse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
    */
    private $num_caisse;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
    */
    private $nom_caisse;
    
    /**
     * @ORM\OneToMany(targetEntity=Treatment::class, mappedBy="id_caisse")
     */
    private $traitements;


    public function __construct()
    {
        $this->traitements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCaisse(): ?string
    {
        return $this->nom_caisse;
    }

    public function setNomCaisse(string $nom_caisse): self
    {
        $this->nom_caisse = $nom_caisse;

        return $this;
    }

    public function getNumCaisse(): ?string
    {
        return $this->num_caisse;
    }

    public function setNumCaisse(string $num_caisse): self
    {
        $this->num_caisse = $num_caisse;

        return $this;
    }

    public function __toString(): string {
        return $this->id;
    }
    
    /**
     * @return Collection<int, Treatment>
     */
    public function getTraitements(): Collection
    {
        return $this->traitements;
    }

    public function addTraitement(Treatment $traitement): self
    {
        if (!$this->traitements->contains($traitement)) {
            $this->traitements[] = $traitement;
            $traitement->setIdCaisse($this);
        }

        return $this;
    }

    public function removeTraitement(Treatment $traitement): self
    {
        if ($this->traitements->removeElement($traitement)) {
            // set the owning side to null (unless already changed)
            if ($traitement->getIdCaisse() === $this) {
                $traitement->setIdCaisse(null);
            }
        }

        return $this;
    }
    
}