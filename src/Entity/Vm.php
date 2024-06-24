<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VmRepository")
 */
class Vm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $nom_vm;
    
    /**
     * @ORM\ManyToOne(targetEntity=PoolVm::class, inversedBy="nom_vm")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poolVm;

    /**
     * @ORM\ManyToMany(targetEntity=Treatment::class, mappedBy="id_vm")
     * @ORM\JoinColumn(nullable=true)
     */
    private $id_traitement;


    public function __construct()
    {
        $this->id_traitement = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->nom_vm;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomVm(): ?string
    {
        return $this->nom_vm;
    }

    public function setNomVm(string $nom_vm): self
    {
        $this->nom_vm = $nom_vm;

        return $this;
    }

    public function getPoolVm(): ?PoolVm
    {
        return $this->poolVm;
    }

    public function setPoolVm(?PoolVm $poolVm): self
    {
        $this->poolVm = $poolVm;

        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getIdTraitement(): Collection
    {
        return $this->id_traitement;
    }

    public function addIdTraitement(?Treatment $idTraitement): self
    {
        if ($idTraitement !== null && !$this->id_traitement->contains($idTraitement)) {
            $this->id_traitement[] = $idTraitement;
            $idTraitement->addIdVm($this);
        }
    
        return $this;
    }
    
    public function removeIdTraitement(Treatment $idTraitement): self
    {
        if ($this->id_traitement->removeElement($idTraitement)) {
            $idTraitement->removeIdVm($this);
        }

        return $this;
    }

}
