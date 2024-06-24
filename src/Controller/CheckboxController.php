<?php

namespace App\Controller;

use App\Entity\Vm;
use App\Entity\Process;
use App\Entity\Scenario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CheckboxController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        date_default_timezone_set('Europe/Paris');
    }


    public function ProcessCheckboxChange(Request $request): JsonResponse
    {
        $processId = $request->query->get('processId');
        $process = $this->entityManager->getRepository(Process::class)->find($processId);

        // Condition pour le calcul de traitement
        if ($process) {

            $checkedProcessIds = $request->query->get('checkedProcessIdsString');
            if ($checkedProcessIds) {
                $checkedProcessIds = explode(',', $checkedProcessIds);
                $tempsMinuteDossier = 0;
                $onlyForMinutes = 0;

                $checkedProcesses = $this->entityManager->getRepository(Process::class)->findBy(['id' => $checkedProcessIds]);

                foreach ($checkedProcesses as $process) {
                    $tempsMinuteDossier += $process->getTempsMinuteDossier();

                    $totalMinutesInDay = 24 * 60;

                    if ($process->getStartTime() !== null && ($process->getEndTime() !== null)) {
                        // pour les process qui tournent entre x heures
                        $interval = $process->getStartTime()->diff($process->getEndTime());

                        // Convertir la différence en minutes
                        $runningTimeMinutes = $interval->h * 60 + $interval->i;

                        // Temps pendant lequel le processus ne tourne pas
                        $onlyForMinutes = $totalMinutesInDay - $runningTimeMinutes;
                    } else {
                        $onlyForMinutes = 24 * 60;
                    }
                }

                $nbreDossier = (int) $request->query->get('nbredossier');
                $checkedVMs =  (int) $request->query->get('checkedVMs');

                if ($checkedVMs > 0) {
                    if ($nbreDossier > 0) {

                        $resultMinutes1  = (($tempsMinuteDossier * $nbreDossier) / $checkedVMs) * (24 * 60 / $onlyForMinutes); 

                        $resultMinutes = $resultMinutes1; 

                        $jours = floor($resultMinutes / (24 * 60));
                        $heures = floor(($resultMinutes % (24 * 60)) / 60);
                        $minutes = $resultMinutes % 60;

                        $formattedResult = "$jours jour(s), $heures heure(s) et $minutes minute(s)";

                        $checkedVmsIdsString = $request->query->get('checkedVmsIdsString');
                        $checkedVmsIds = explode(',', $checkedVmsIdsString);

                        // CALCULER LE DERNIER TRAITEMENT ACTUEL AVEC LES VMS COCHEES

                        $checkedVmsIdsString = $request->query->get('checkedVmsIdsString');
                        $checkedVmsIds = explode(',', $checkedVmsIdsString);

                        $checkedVms = $this->entityManager->getRepository(Vm::class)->findBy(['id' => $checkedVmsIds]);

                        $traitementsAssocies = [];

                        foreach ($checkedVms as $vm) {
                            $traitements = $vm->getIdTraitement();
                        
                            foreach ($traitements as $traitement) {
                                // Vérifier si le traitement a un parent
                                $parentTraitement = $traitement->getParentTraitement();
                                
                                // Si le traitement n'a pas de parent, alors c'est un traitement parent
                                if ($parentTraitement === null) {
                                    $traitementId = $traitement->getId();                        
                                    $traitementDateDebut = $traitement->getDateDebut();
                                    $traitementDateFin = $traitement->getDateFin();
                        
                                    $traitementsAssocies[] = [
                                        'id' => $traitementId,
                                        'dateDebut' => $traitementDateDebut,
                                        'dateFin' => $traitementDateFin,
                                    ];
                                }
                            }
                        }

                        $responseData = [
                            'success' => true,
                            'message' => 'Calcul terminé',
                            'result' => $formattedResult,
                            'result1' => $resultMinutes,
                            'onlyForMinutes' => $onlyForMinutes,
                            'checkedVMs' => $checkedVMs,
                            'checkedProcessIds' => $checkedProcessIds,
                            'checkedVmsIds' => $checkedVmsIds,
                            'traitementsAssocies' => $traitementsAssocies
                        ];
                    } else {
                        $responseData = [
                            'error' => true,
                            'message' => 'Nombre de dossiers non transmis.',
                        ];
                    }
                } else {
                    $responseData = [
                        'error' => true,
                        'message' => 'Sélectionné au moins une vm pour le calcul.',
                    ];
                }
            } else {
                $responseData = [
                    'error' => true,
                    'message' => 'Process non trouvé.',
                ];
            }
        } else {
            $responseData = [
                'error' => true,
                'message' => 'Process non trouvé.',
            ];
        }

        return new JsonResponse($responseData);
    }








    public function VmCheckboxChange(Request $request): JsonResponse
    {
        $vmId = $request->query->get('vmId');
        $vm = $this->entityManager->getRepository(Vm::class)->find($vmId);

        if ($vm) {

            $checkedProcessIds = $request->query->get('checkedProcessIdsString');
            if ($checkedProcessIds) {

                $checkedProcessIds = explode(',', $checkedProcessIds);
                $tempsMinuteDossier = 0;
                $onlyForMinutes = 0;

                $checkedProcesses = $this->entityManager->getRepository(Process::class)->findBy(['id' => $checkedProcessIds]);

                foreach ($checkedProcesses as $process) {
                    $tempsMinuteDossier += $process->getTempsMinuteDossier();

                    $totalMinutesInDay = 24 * 60;

                    if ($process->getStartTime() !== null && ($process->getEndTime() !== null)) {
                        // pour les process qui tournent entre x heures
                        $interval = $process->getStartTime()->diff($process->getEndTime());

                        // Convertir la différence en minutes
                        $runningTimeMinutes = $interval->h * 60 + $interval->i;

                        // Temps pendant lequel le processus ne tourne pas
                        $onlyForMinutes = $totalMinutesInDay - $runningTimeMinutes;
                    } else {
                        $onlyForMinutes = 24 * 60;
                    }
                }

                $nbreDossier = (int) $request->query->get('nbredossier');
                $checkedVMs = (int) $request->query->get('checkedVMs');
                $checkedProcess = (int) $request->query->get('checkedProcess');

                if ($nbreDossier > 0) {
                    if ($checkedProcess && $checkedVMs > 0) {

                        $resultMinutes1  = (($tempsMinuteDossier * $nbreDossier) / $checkedVMs) * (24 * 60 / $onlyForMinutes); 

                        $resultMinutes = $resultMinutes1;

                        $jours = floor($resultMinutes / (24 * 60));
                        $heures = floor(($resultMinutes % (24 * 60)) / 60);
                        $minutes = $resultMinutes % 60;

                        $formattedResult = "$jours jour(s), $heures heure(s) et $minutes minute(s)";

                        // CALCULER LE DERNIER TRAITEMENT ACTUEL AVEC LES VMS COCHEES

                        $checkedVmsIdsString = $request->query->get('checkedVmsIdsString');
                        $checkedVmsIds = explode(',', $checkedVmsIdsString);

                        $checkedVms = $this->entityManager->getRepository(Vm::class)->findBy(['id' => $checkedVmsIds]);

                        $traitementsAssocies = [];

                        foreach ($checkedVms as $vm) {
                            $traitements = $vm->getIdTraitement();
                        
                            foreach ($traitements as $traitement) {
                                // Vérifier si le traitement a un parent
                                $parentTraitement = $traitement->getParentTraitement();
                                
                                // Si le traitement n'a pas de parent, alors c'est un traitement parent
                                if ($parentTraitement === null) {
                                    $traitementId = $traitement->getId();                        
                                    $traitementDateDebut = $traitement->getDateDebut();
                                    $traitementDateFin = $traitement->getDateFin();
                        
                                    $traitementsAssocies[] = [
                                        'id' => $traitementId,
                                        'dateDebut' => $traitementDateDebut,
                                        'dateFin' => $traitementDateFin,
                                    ];
                                }
                            }
                        }

                        $responseData = [
                            'success' => true,
                            'message' => 'Calcul terminé',
                            'result' => $formattedResult,
                            'result1' => $resultMinutes,
                            'checkedProcessIds' => $checkedProcessIds,
                            'checkedVmsIds' => $checkedVmsIds,
                            'traitementsAssocies' => $traitementsAssocies
                        ];
                    } else {
                        $responseData = [
                            'error' => true,
                            'message' => 'Sélectionné au moins un process pour le calcul.',
                        ];
                    }
                } else {
                    $responseData = [
                        'error' => true,
                        'message' => 'Nombre de dossiers non transmis.',
                    ];
                }
            } else {
                $responseData = [
                    'error' => true,
                    'message' => 'Vm non trouvée.',
                ];
            }
        } else {
            $responseData = [
                'error' => true,
                'message' => 'Vm non trouvée.',
            ];
        }

        return new JsonResponse($responseData);
    }




























    public function nbreDossierChange(Request $request): JsonResponse
    {
        $nbreDossier = (int) $request->query->get('nbreDossier');
        $checkedVMs =  (int) $request->query->get('checkedVMs');
        $checkedProcess =  (int) $request->query->get('checkedProcess');


        if ($checkedVMs > 0 && $checkedProcess > 0) {
            if ($nbreDossier > 0) {
                $checkedProcessIds = $request->query->get('checkedProcessIdsString');
                $checkedProcessIds = explode(',', $checkedProcessIds);
                $tempsMinuteDossier = 0;
                $onlyForMinutes = 0;

                $checkedProcesses = $this->entityManager->getRepository(Process::class)->findBy(['id' => $checkedProcessIds]);

                foreach ($checkedProcesses as $process) {
                    $tempsMinuteDossier += $process->getTempsMinuteDossier();

                    $totalMinutesInDay = 24 * 60;

                    if ($process->getStartTime() !== null && ($process->getEndTime() !== null)) {
                        // pour les process qui tournent entre x heures
                        $interval = $process->getStartTime()->diff($process->getEndTime());

                        // Convertir la différence en minutes
                        $runningTimeMinutes = $interval->h * 60 + $interval->i;

                        // Temps pendant lequel le processus ne tourne pas
                        $onlyForMinutes = $totalMinutesInDay - $runningTimeMinutes;
                    } else {
                        $onlyForMinutes = 24 * 60;
                    }
                }

                $resultMinutes1  = (($tempsMinuteDossier * $nbreDossier) / $checkedVMs) * (24 * 60 / $onlyForMinutes); 

                $resultMinutes = $resultMinutes1;

                $jours = floor($resultMinutes / (24 * 60));
                $heures = floor(($resultMinutes % (24 * 60)) / 60);
                $minutes = $resultMinutes % 60;

                $formattedResult = "$jours jour(s), $heures heure(s) et $minutes minute(s)";


                // CALCULER LE DERNIER TRAITEMENT ACTUEL AVEC LES VMS COCHEES

                $checkedVmsIdsString = $request->query->get('checkedVmsIdsString');
                $checkedVmsIds = explode(',', $checkedVmsIdsString);

                $checkedVms = $this->entityManager->getRepository(Vm::class)->findBy(['id' => $checkedVmsIds]);

                $traitementsAssocies = [];

                foreach ($checkedVms as $vm) {
                    $traitements = $vm->getIdTraitement();
                
                    foreach ($traitements as $traitement) {
                        // Vérifier si le traitement a un parent
                        $parentTraitement = $traitement->getParentTraitement();
                        
                        // Si le traitement n'a pas de parent, alors c'est un traitement parent
                        if ($parentTraitement === null) {
                            $traitementId = $traitement->getId();                        
                            $traitementDateDebut = $traitement->getDateDebut();
                            $traitementDateFin = $traitement->getDateFin();
                
                            $traitementsAssocies[] = [
                                'id' => $traitementId,
                                'dateDebut' => $traitementDateDebut,
                                'dateFin' => $traitementDateFin,
                            ];
                        }
                    }
                }

                $responseData = [
                    'success' => true,
                    'message' => 'Calcul terminé',
                    'result' => $formattedResult,
                    'result1' => $resultMinutes,
                    'checkedVMs' => $checkedVMs,
                    'checkedProcessIds' => $checkedProcessIds,
                    'checkedVmsIds' => $checkedVmsIds,
                    'traitementsAssocies' => $traitementsAssocies
                ];
            } else {
                $responseData = [
                    'error' => true,
                    'message' => 'Nombre de dossiers non transmis.',
                ];
            }
        } else {
            $responseData = [
                'error' => true,
                'message' => 'Sélectionné au moins une vm et un process pour le calcul.',
            ];
        }

        return new JsonResponse($responseData);
    }  







    public function selectAllCheckbox(Request $request): JsonResponse
    {
        $nbreDossier = (int) $request->query->get('nbreDossier');
        $checkedVMs =  (int) $request->query->get('checkedVMs');
        $checkedProcess =  (int) $request->query->get('checkedProcess');


        if ($checkedVMs > 0 && $checkedProcess > 0) {
            if ($nbreDossier > 0) {
                $checkedProcessIds = $request->query->get('checkedProcessIdsString');
                $checkedProcessIds = explode(',', $checkedProcessIds);
                $tempsMinuteDossier = 0;
                $onlyForMinutes = 0;

                $checkedProcesses = $this->entityManager->getRepository(Process::class)->findBy(['id' => $checkedProcessIds]);

                foreach ($checkedProcesses as $process) {
                    $tempsMinuteDossier += $process->getTempsMinuteDossier();

                    $totalMinutesInDay = 24 * 60;

                    if ($process->getStartTime() !== null && ($process->getEndTime() !== null)) {
                        // pour les process qui tournent entre x heures
                        $interval = $process->getStartTime()->diff($process->getEndTime());

                        // Convertir la différence en minutes
                        $runningTimeMinutes = $interval->h * 60 + $interval->i;

                        // Temps pendant lequel le processus ne tourne pas
                        $onlyForMinutes = $totalMinutesInDay - $runningTimeMinutes;
                    } else {
                        $onlyForMinutes = 24 * 60;
                    }
                }

                $resultMinutes1  = (($tempsMinuteDossier * $nbreDossier) / $checkedVMs) * (24 * 60 / $onlyForMinutes); 

                $resultMinutes = $resultMinutes1;

                $jours = floor($resultMinutes / (24 * 60));
                $heures = floor(($resultMinutes % (24 * 60)) / 60);
                $minutes = $resultMinutes % 60;

                $formattedResult = "$jours jour(s), $heures heure(s) et $minutes minute(s)";

                // CALCULER LE DERNIER TRAITEMENT ACTUEL AVEC LES VMS COCHEES

                $checkedVmsIdsString = $request->query->get('checkedVmsIdsString');
                $checkedVmsIds = explode(',', $checkedVmsIdsString);

                $checkedVms = $this->entityManager->getRepository(Vm::class)->findBy(['id' => $checkedVmsIds]);

                $traitementsAssocies = [];

                foreach ($checkedVms as $vm) {
                    $traitements = $vm->getIdTraitement();
                
                    foreach ($traitements as $traitement) {
                        // Vérifier si le traitement a un parent
                        $parentTraitement = $traitement->getParentTraitement();
                        
                        // Si le traitement n'a pas de parent, alors c'est un traitement parent
                        if ($parentTraitement === null) {
                            $traitementId = $traitement->getId();                        
                            $traitementDateDebut = $traitement->getDateDebut();
                            $traitementDateFin = $traitement->getDateFin();
                
                            $traitementsAssocies[] = [
                                'id' => $traitementId,
                                'dateDebut' => $traitementDateDebut,
                                'dateFin' => $traitementDateFin,
                            ];
                        }
                    }
                }

                $responseData = [
                    'success' => true,
                    'message' => 'Calcul terminé',
                    'result' => $formattedResult,
                    'result1' => $resultMinutes,
                    'checkedVMs' => $checkedVMs,
                    'checkedProcessIds' => $checkedProcessIds,
                    'checkedVmsIds' => $checkedVmsIds,
                    'traitementsAssocies' => $traitementsAssocies
                ];
            } else {
                $responseData = [
                    'error' => true,
                    'message' => 'Nombre de dossiers non transmis.',
                ];
            }
        } else {
            $responseData = [
                'error' => true,
                'message' => 'Sélectionné au moins une vm et un process pour le calcul.',
            ];
        }

        return new JsonResponse($responseData);
    }
























    public function dateDebutChange(Request $request): JsonResponse
    {
        $nbreDossier = (int) $request->query->get('nbreDossier');
        $checkedVMs =  (int) $request->query->get('checkedVMs');
        $checkedProcess =  (int) $request->query->get('checkedProcess');


        if ($checkedVMs > 0 && $checkedProcess > 0) {
            if ($nbreDossier > 0) {
                $checkedProcessIds = $request->query->get('checkedProcessIdsString');
                $checkedProcessIds = explode(',', $checkedProcessIds);
                $tempsMinuteDossier = 0;
                $onlyForMinutes = 0;

                $checkedProcesses = $this->entityManager->getRepository(Process::class)->findBy(['id' => $checkedProcessIds]);

                foreach ($checkedProcesses as $process) {
                    $tempsMinuteDossier += $process->getTempsMinuteDossier();

                    $totalMinutesInDay = 24 * 60;

                    if ($process->getStartTime() !== null && ($process->getEndTime() !== null)) {
                        // pour les process qui tournent entre x heures
                        $interval = $process->getStartTime()->diff($process->getEndTime());

                        // Convertir la différence en minutes
                        $runningTimeMinutes = $interval->h * 60 + $interval->i;

                        // Temps pendant lequel le processus ne tourne pas
                        $onlyForMinutes = $totalMinutesInDay - $runningTimeMinutes;
                    } else {
                        $onlyForMinutes = 24 * 60;
                    }
                }

                $resultMinutes1  = (($tempsMinuteDossier * $nbreDossier) / $checkedVMs) * (24 * 60 / $onlyForMinutes); 

                $resultMinutes = $resultMinutes1;

                $jours = floor($resultMinutes / (24 * 60));
                $heures = floor(($resultMinutes % (24 * 60)) / 60);
                $minutes = $resultMinutes % 60;

                $formattedResult = "$jours jour(s), $heures heure(s) et $minutes minute(s)";

                // CALCULER LE DERNIER TRAITEMENT ACTUEL AVEC LES VMS COCHEES

                $checkedVmsIdsString = $request->query->get('checkedVmsIdsString');
                $checkedVmsIds = explode(',', $checkedVmsIdsString);

                $checkedVms = $this->entityManager->getRepository(Vm::class)->findBy(['id' => $checkedVmsIds]);

                $traitementsAssocies = [];

                foreach ($checkedVms as $vm) {
                    $traitements = $vm->getIdTraitement();
                
                    foreach ($traitements as $traitement) {
                        // Vérifier si le traitement a un parent
                        $parentTraitement = $traitement->getParentTraitement();
                        
                        // Si le traitement n'a pas de parent, alors c'est un traitement parent
                        if ($parentTraitement === null) {
                            $traitementId = $traitement->getId();                        
                            $traitementDateDebut = $traitement->getDateDebut();
                            $traitementDateFin = $traitement->getDateFin();
                
                            $traitementsAssocies[] = [
                                'id' => $traitementId,
                                'dateDebut' => $traitementDateDebut,
                                'dateFin' => $traitementDateFin,
                            ];
                        }
                    }
                }

                $responseData = [
                    'success' => true,
                    'message' => 'Calcul terminé',
                    'result' => $formattedResult,
                    'result1' => $resultMinutes,
                    'checkedVMs' => $checkedVMs,
                    'checkedProcessIds' => $checkedProcessIds,
                    'checkedVmsIds' => $checkedVmsIds,
                    'traitementsAssocies' => $traitementsAssocies
                ];
            } else {
                $responseData = [
                    'error' => true,
                    'message' => 'Nombre de dossiers non transmis.',
                ];
            }
        } else {
            $responseData = [
                'error' => true,
                'message' => 'Sélectionné au moins une vm et un process pour le calcul.',
            ];
        }

        return new JsonResponse($responseData);
    } 































    public function VmMax(Request $request): JsonResponse
    {
        $checkedProcessIds = $request->query->get('checkedProcessIdsString');
        $checkedProcessIds = explode(',', $checkedProcessIds);

        $vm_max_values = [];
        $processNames = [];

        if(!empty($checkedProcessIds) && $checkedProcessIds[0] !== "") {
            $checkedProcesses = $this->entityManager->getRepository(Process::class)->findBy(['id' => $checkedProcessIds]);

            foreach ($checkedProcesses as $process) {
                $vm_max_values[] = $process->getVmMax();
                $processNames[] = $process->getNomProcess();
            }

            $responseData = [
                'success' => true,
                'checkedProcessIds' => $checkedProcessIds,
                'processNames' => $processNames,
                'vm_max' => $vm_max_values,
            ];
        } 
        else {
            $responseData = [
                'error' => true,
                'message' => 'Aucun process trouvé qui comporte uniquement une seule vm.',
            ];
        }

        return new JsonResponse($responseData);
    }







    public function WeekendsAndHolidays(Request $request): JsonResponse
    {
        $checkedProcessIds = $request->query->get('checkedProcessIdsString');
        $checkedProcessIds = explode(',', $checkedProcessIds);

        if(!empty($checkedProcessIds) && $checkedProcessIds[0] !== "") {
            // Récupérer les process avec weekdsends_and_holidays à true et avec les IDs spécifiés
            $processesWithWeekendsAndHolidays =  $this->entityManager->getRepository(Process::class)->findBy([
                'weekdsends_and_holidays' => true,
                'id' => $checkedProcessIds
            ]);

            // Construire une liste des noms de ces process
            $processNames = [];
            foreach ($processesWithWeekendsAndHolidays as $process) {
                $processNames[] = $process->getNomProcess();
            }

            if(!empty($processNames)) {
                $responseData = [
                    'success' => true,
                    'checkedProcessIds' => $checkedProcessIds,
                    'processIdsWithWeekendsAndHolidays' => $processNames
                ];
            } else {
                $responseData = [
                    'error' => true,
                    'message' => 'Aucun process trouvé qui ne tourne pas les week ends et jour fériés.',
                ];
            }
            
        } else {
            $responseData = [
                'error' => true,
                'message' => 'Aucun process trouvé.',
            ];
        }

        return new JsonResponse($responseData);
    }
















    public function EtapeSelect(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $scenarioId = (int) $request->query->get('scenarioId');
        $selectedEtape = $request->query->get('selectedEtape');
        $nbreDossier = (int) $request->query->get('nbreDossier');

        // Récupérer le scénario correspondant à l'ID
        $scenario = $entityManager->getRepository(Scenario::class)->find($scenarioId);

        if (!$scenario) {
            $responseData = [
                'error' => true,
                'message' => 'Scénario non trouvé.',
            ];
            return new JsonResponse($responseData);
        }

        // Récupérer les délais correspondants au scénario
        $delai1 = $scenario->getDelai1();
        $delai2 = $scenario->getDelai2();
        $delai3 = $scenario->getDelai3();

        $responseData = [
            'success' => true,
            'selectedEtape' => $selectedEtape,
            'scenarioId' => $scenarioId,
            'delai1' => $delai1,
            'delai2' => $delai2,
            'delai3' => $delai3,
            'nbreDossier' => $nbreDossier
        ];

        return new JsonResponse($responseData);
    } 


    public function Periodicity(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $scenarioId = (int) $request->query->get('selectedScenarioId');
    
        // Récupérer le scénario correspondant à l'ID
        $scenario = $entityManager->getRepository(Scenario::class)->find($scenarioId);
    
        if (!$scenario) {
            $responseData = [
                'error' => true,
                'message' => 'Scénario non trouvé.',
            ];
            return new JsonResponse($responseData);
        }
    
        $scenarioName = $scenario->getNomScenario();
        $periodicity = $scenario->getPeriodicity();
        $periodicity_until = $scenario->getUntilWhen();
        
        // Vérifier si le nom du scénario contient "puma"
        if (stripos($scenarioName, 'puma') === false) {
            $responseData = [
                'success' => true,
                'periodicity' => $periodicity,
                'periodicity_until' => $periodicity_until,
            ];
        } else {
            $responseData = [
                'error' => true
            ];
        }
    
        return new JsonResponse($responseData);
    }

}

















