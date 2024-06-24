<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TreatmentRepository")
 */
class Treatment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
    */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbre_dossier;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $auteur;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $error_comment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_fin_error;
    
    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $cloture_comment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_fin_cloture;

    /**
     * @ORM\ManyToOne(targetEntity=CaisseExploit::class, inversedBy="traitements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_caisse_exploit;

    /**
     * @ORM\ManyToOne(targetEntity=Caisse::class, inversedBy="traitements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_caisse;

    /**
     * @ORM\ManyToMany(targetEntity=PoolVm::class, inversedBy="traitements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_pool;

    /**
     * @ORM\ManyToOne(targetEntity=Scenario::class, inversedBy="id_traitement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_scenario;

    /**
     * @ORM\ManyToMany(targetEntity=Process::class, inversedBy="id_traitement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_process;

    /**
     * @ORM\ManyToMany(targetEntity=Vm::class, inversedBy="id_traitement")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_vm;

    /**
     * @ORM\OneToMany(targetEntity="Treatment", mappedBy="parentTraitement", cascade={"persist", "remove"})
     */
    private $replanifications;

    /**
     * @ORM\ManyToOne(targetEntity="Treatment", inversedBy="replanifications")
     * @ORM\JoinColumn(name="parent_traitement_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentTraitement;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $etape;

    public function __construct()
    {
        $this->id_pool = new ArrayCollection();
        $this->id_vm = new ArrayCollection();
        $this->id_process = new ArrayCollection();
        $this->replanifications = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getNbreDossier(): ?int
    {
        return $this->nbre_dossier;
    }

    public function setNbreDossier(?int $nbre_dossier): self
    {
        $this->nbre_dossier = $nbre_dossier;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
    
    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getErrorComment(): ?string
    {
        return $this->error_comment;
    }

    public function setErrorComment(?string $error_comment): self
    {
        $this->error_comment = $error_comment;

        return $this;
    }

    public function getDateFinError(): ?\DateTimeInterface
    {
        return $this->date_fin_error;
    }
    
    public function setDateFinError(?\DateTimeInterface $date_fin_error): self
    {
        $this->date_fin_error = $date_fin_error;

        return $this;
    }

    public function removeDateFinError(): self
    {
        $this->date_fin_error = null;

        return $this;
    }

    public function getClotureComment(): ?string
    {
        return $this->cloture_comment;
    }

    public function setClotureComment(?string $cloture_comment): self
    {
        $this->cloture_comment = $cloture_comment;

        return $this;
    }

    public function getDateFinCloture(): ?\DateTimeInterface
    {
        return $this->date_fin_cloture;
    }

    public function setDateFinCloture(?\DateTimeInterface $date_fin_cloture): self
    {
        $this->date_fin_cloture = $date_fin_cloture;

        return $this;
    }

    public function removeDateFinCloture(): self
    {
        $this->date_fin_cloture = null;

        return $this;
    }

    public function getIdCaisse(): ?Caisse
    {
        return $this->id_caisse;
    }

    public function setIdCaisse(?Caisse $id_caisse): self
    {
        $this->id_caisse = $id_caisse;

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


    public function getIdScenario(): ?Scenario
    {
        return $this->id_scenario;
    }

    public function setIdScenario(?Scenario $id_scenario): self
    {
        $this->id_scenario = $id_scenario;

        return $this;
    }

    public function getEtape(): ?String
    {
        return $this->etape;
    }

    public function setEtape(?String $etape): self
    {
        $this->etape = $etape;

        return $this;
    }

    /**
     * @return Collection<int, Process>
     */
    public function getIdProcess(): Collection
    {
        return $this->id_process;
    }

    public function addIdProcess(Process $idProcess): self
    {
        if (!$this->id_process->contains($idProcess)) {
            $this->id_process[] = $idProcess;
        }

        return $this;
    }

    public function removeIdProcess(Process $idProcess): self
    {
        $this->id_process->removeElement($idProcess);

        return $this;
    }

    /**
     * @return Collection<int, Vm>
     */
    public function getIdVm(): Collection
    {
        return $this->id_vm;
    }

    public function addIdVm(Vm $idVm): self
    {
        if (!$this->id_vm->contains($idVm)) {
            $this->id_vm[] = $idVm;
        }

        return $this;
    }

    public function removeIdVm(Vm $idVm): self
    {
        $this->id_vm->removeElement($idVm);

        return $this;
    }



    /**
     * @return Collection<int, PoolVm>
     */
    public function getIdPool(): Collection
    {
        return $this->id_pool;
    }

    public function addIdPool(PoolVm $idPool): self
    {
        if (!$this->id_pool->contains($idPool)) {
            $this->id_pool[] = $idPool;
        }

        return $this;
    }
    
    public function removeIdPool(PoolVm $idPool): self
    {
        $this->id_pool->removeElement($idPool);

        return $this;
    }

    /**
     * @return Collection|Treatment[]
     */


    public function getReplanifications(): Collection
    {
        return $this->replanifications;
    }

    public function addReplanification(Treatment $replanification): self
    {
        if (!$this->replanifications->contains($replanification)) {
            $this->replanifications[] = $replanification;
            $replanification->setParentTraitement($this);
        }

        return $this;
    }

    public function removeReplanification(Treatment $replanification): self
    {
        if ($this->replanifications->removeElement($replanification)) {
            // set the owning side to null (unless already changed)
            if ($replanification->getParentTraitement() === $this) {
                $replanification->setParentTraitement(null);
            }
        }

        return $this;
    }

    public function getParentTraitement(): ?self
    {
        return $this->parentTraitement;
    }

    public function setParentTraitement(?self $parentTraitement): self
    {
        $this->parentTraitement = $parentTraitement;

        return $this;
    }

}