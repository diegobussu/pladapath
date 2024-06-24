<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PoolVmRepository")
 */
class PoolVm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $nom_pool;
   
    /**
     * @ORM\ManyToMany(targetEntity=Treatment::class, mappedBy="id_pool")
     * @ORM\JoinColumn(nullable=true)
     */
    private $traitements;

    /**
     * @ORM\OneToMany(targetEntity=Vm::class, mappedBy="poolVm")
     */
    private $nom_vm;

    /**
     * @ORM\ManyToOne(targetEntity=CaisseExploit::class, inversedBy="id_poolvm")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_caisse_exploit;

    

    public function __construct()
    {

        $this->traitements = new ArrayCollection();
        $this->nom_vm = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPool(): ?string
    {
        return $this->nom_pool;
    }

    public function setNomPool(string $nom_pool): self
    {
        $this->nom_pool = $nom_pool;

        return $this;
    }

    public function __toString(): string {
        return $this->id;
    }



    /**
     * @return Collection<int, Treatment>
     */
    public function getIdTraitement(): Collection
    {
        return $this->traitements;
    }

    public function addIdTraitement(?Treatment $idTraitement): self
    {
        if ($idTraitement !== null && !$this->traitements->contains($idTraitement)) {
            $this->traitements[] = $idTraitement;
            $idTraitement->addIdPool($this);
        }
    
        return $this;
    }
    
    public function removeIdTraitement(Treatment $idTraitement): self
    {
        if ($this->traitements->removeElement($idTraitement)) {
            $idTraitement->removeIdPool($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vm>
     */
    public function getNomVm(): Collection
    {
        return $this->nom_vm;
    }

    public function addNomVm(Vm $nomVm): self
    {
        if (!$this->nom_vm->contains($nomVm)) {
            $this->nom_vm[] = $nomVm;
            $nomVm->setPoolVm($this);
        }

        return $this;
    }

    public function removeNomVm(Vm $nomVm): self
    {
        if ($this->nom_vm->removeElement($nomVm)) {
            // set the owning side to null (unless already changed)
            if ($nomVm->getPoolVm() === $this) {
                $nomVm->setPoolVm(null);
            }
        }

        return $this;
    }

    public function getIdCaisseExploit(): ?CaisseExploit
    {
        return $this->id_caisse_exploit;
    }

    public function setIdCaisseExploit(?CaisseExploit $id_caisse_exploit): self
    {
        $this->id_caisse_exploit = $id_caisse_exploit;

        return $this;
    }

}
