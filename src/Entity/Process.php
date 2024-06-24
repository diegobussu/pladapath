<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProcessRepository")
 */
class Process
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom_process;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temps_minute_dossier;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $vm_max;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $weekdsends_and_holidays;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $start_time;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $end_time;

    /**
     * @ORM\ManyToMany(targetEntity=Scenario::class, inversedBy="processes")
     */
    private $id_scenario;

    /**
     * @ORM\ManyToMany(targetEntity=Treatment::class, mappedBy="id_process")
     */
    private $id_traitement;



    public function __construct()
    {
        $this->id_scenario = new ArrayCollection();
        $this->id_traitement = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom_process;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProcess(): ?string
    {
        return $this->nom_process;
    }

    public function setNomProcess(string $nom_process): self
    {
        $this->nom_process = $nom_process;

        return $this;
    }

    public function getTempsMinuteDossier(): ?int
    {
        return $this->temps_minute_dossier;
    }

    public function setTempsMinuteDossier(?int $temps_minute_dossier): self
    {
        $this->temps_minute_dossier = $temps_minute_dossier;

        return $this;
    }

    public function getVmMax(): ?bool
    {
        return $this->vm_max;
    }

    public function setVmMax(?bool $vm_max): self
    {
        $this->vm_max = $vm_max;

        return $this;
    }
    
    public function getWeekdsendsAndHolidays(): ?bool
    {
        return $this->weekdsends_and_holidays;
    }

    public function setWeekdsendsAndHolidays(?bool $weekdsends_and_holidays): self
    {
        $this->weekdsends_and_holidays = $weekdsends_and_holidays;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface 
    {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface  
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

        return $this;
    }

    /**
     * @return Collection<int, Scenario>
     */
    public function getIdScenario(): Collection
    {
        return $this->id_scenario;
    }

    public function addIdScenario(Scenario $idScenario): self
    {
        if (!$this->id_scenario->contains($idScenario)) {
            $this->id_scenario[] = $idScenario;
            $idScenario->addProcess($this);
        }

        return $this;
    }

    public function removeIdScenario(Scenario $idScenario): self
    {
        if ($this->id_scenario->removeElement($idScenario)) {
            $idScenario->removeProcess($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getIdTraitement(): Collection
    {
        return $this->id_traitement;
    }

    public function addIdTraitement(Treatment $idTraitement): self
    {
        if (!$this->id_traitement->contains($idTraitement)) {
            $this->id_traitement[] = $idTraitement;
            $idTraitement->addIdProcess($this);
        }

        return $this;
    }

    public function removeIdTraitement(Treatment $idTraitement): self
    {
        if ($this->id_traitement->removeElement($idTraitement)) {
            $idTraitement->removeIdProcess($this);
        }

        return $this;
    }
}
