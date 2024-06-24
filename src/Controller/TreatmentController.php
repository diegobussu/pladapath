<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Caisse;
use App\Entity\CaisseExploit;
use App\Entity\Scenario;
use App\Entity\PoolVm;
use App\Entity\Vm;
use App\Entity\Process;
use App\Entity\Treatment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Logs;
use App\Repository\CaisseExploitRepository;
use App\Repository\CaisseRepository;
use App\Repository\PoolVmRepository;
use App\Repository\ScenarioRepository;
use Dompdf\Dompdf;
use Dompdf\Options;


class TreatmentController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    
    private function getTotalVmCountForPumaScenariosByDate(EntityManagerInterface $entityManager, \DateTime $date): int
    {
        $scenarioContains = 'puma';
        $totalVmCount = 0;
    
        $scenarios = $entityManager->getRepository(Scenario::class)->findByCriteria($scenarioContains, $date);
    
        foreach ($scenarios as $scenario) {
            foreach ($scenario->getIdTraitement() as $traitement) {
                // Ajoutez une vérification pour la date du traitement
                if ($traitement->getDateDebut() <= $date && $traitement->getDateFin() >= $date && $traitement->getEtat() === 'En cours') {
                    $vms = $traitement->getIdVm();
                    $totalVmCount += count($vms);
                }
            }
        }
    
        return $totalVmCount;
    }    


    public function createTraitment(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $idCaisse = $request->request->get('caisse');
        $idCaisseExploit = $request->request->get('caisse_exploit');

        $selectedPool = $request->request->get('pool', []);

        $idScenario = $request->request->get('scenario');

        $selectedVMs = $request->request->get('vm', []);
        $selectedProcesses = $request->request->get('process', []);

        $nbreDossier = $request->request->get('nbredossier');
        $dateDebut = new \DateTime($request->request->get('datedebut'));
        $dateFin = new \DateTime($request->request->get('datefin'));


        // Créer une instance de l'entité Treatment
        $traitement = new Treatment();

        // définition de l'auteur du traitement
        $traitement->setAuteur($user->getNom() . ' ' . $user->getPrenom());

        $traitement->setDateCreation(new \DateTime());

        $traitement->setDateDebut($dateDebut);
        $traitement->setDateFin($dateFin);

        // Obtenez les entités Caisse et PoolVm à partir de leur ID (suppose que ces entités existent)
        $caisse = $entityManager->getRepository(Caisse::class)->find($idCaisse);
        $caisseExploit = $entityManager->getRepository(CaisseExploit::class)->find($idCaisseExploit);
        $scenario = $entityManager->getRepository(Scenario::class)->find($idScenario);
            
        $traitement->setEtat('En cours');
        $traitement->setErrorComment(null);
        $traitement->setClotureComment(null);

        if (empty($nbreDossier)) {
            $traitement->setNbreDossier('0');
        } else {
            $nbreDossier = intval($nbreDossier);
            $traitement->setNbreDossier($nbreDossier);
        }

        if (!$caisse) {
            $this->addFlash('error', 'Caisse non trouvé.');
            return $this->redirectToRoute('home');
        } $traitement->setIdCaisse($caisse);

        if (!$caisseExploit) {
            $this->addFlash('error', 'Caisse exploitante non trouvé.');
            return $this->redirectToRoute('home');
        } $traitement->setIdCaisseExploit($caisseExploit);

        if (!$scenario) {
            $this->addFlash('error', 'Scenario non trouvé.');
            return $this->redirectToRoute('home');
        } $traitement->setIdScenario($scenario);

        if (!is_array($selectedPool)) {
            $this->addFlash('error', 'Sélection de pool invalide.');
            return $this->redirectToRoute('home');
        }

        if (!is_array($selectedProcesses)) {
            $this->addFlash('error', 'Sélection de process invalide.');
            return $this->redirectToRoute('home');
        }

        if (!is_array($selectedVMs)) {
            $this->addFlash('error', 'Sélection de vm invalide.');
            return $this->redirectToRoute('home');
        }
        
        $filteredPool = [];
        foreach ($selectedPool as $poolId) {
            if ($poolId !== 'Tous') {
                $pool = $entityManager->getRepository(PoolVm::class)->find($poolId);
                $filteredPool[] = $pool;
            }
        }
        
        if (empty($filteredPool)) {
            $this->addFlash('error', 'Pool non trouvé.');
            return $this->redirectToRoute('home');  
        } else {
            foreach ($filteredPool as $pool) {
                $traitement->addIdPool($pool);
                $pool->addIdTraitement($traitement);
            }
        }


        // Filtrer les VM en fonction de la pool
        $filteredVMs = [];
        $vmNamesInError = [];
        foreach ($selectedVMs as $vmId) {
            $vm = $entityManager->getRepository(Vm::class)->find($vmId);

            // Vérifier si la VM est déjà incluse dans un traitement pendant la période spécifiée
            $existingTraitement = $entityManager->getRepository(Treatment::class)->findTraitementForVmAndDateRange($vm, $dateDebut, $dateFin);

            if ($existingTraitement) {
                // Ajouter le nom de la VM à la liste d'erreurs
                $vmNamesInError[] = $vm->getNomVm();
            }

            if ($vm && in_array($vm->getPoolVm()->getId(), $selectedPool)) {
                $filteredVMs[] = $vm;
            }
        }

        if (empty($filteredVMs)) {
            $this->addFlash('error', 'VM non trouvé.');
            return $this->redirectToRoute('home');  
        } else {
            foreach ($filteredVMs as $vm) {
                $traitement->addIdVm($vm);
                $vm->addIdTraitement($traitement);
            }
        }






        $nomProcessVmMax = [];
        $nomProcess = [];

        foreach ($selectedProcesses as $processId) {
            $process = $entityManager->getRepository(Process::class)->find($processId);
        
            // Ajoutez la vérification de l'attribut vm_max ici
            if ($process && $process->getVmMax() && count($filteredVMs) > 1) {
                $nomProcessVmMax[] = $process->getNomProcess();
            }

            if ($process && $process->getWeekdsendsAndHolidays()) {

                if ($dateDebut->format('N') >= 6 || $dateFin->format('N') >= 6) {
                    $nomProcess[] = $process->getNomProcess();
                }

                $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';
                $holidaysData = file_get_contents($holidaysFilePath);
                $holidays = json_decode($holidaysData, true);

                $traitementDates = [$traitement->getDateDebut()->format('Y-m-d'), $traitement->getDateFin()->format('Y-m-d')];
                foreach ($traitementDates as $date) {
                    if (array_key_exists($date, $holidays)) {
                        $nomProcess[] = $process->getNomProcess();
                    }
                }
            }
        }

        if(!empty($nomProcessVmMax)) {
            $this->addFlash('error', 'Le processus ' . implode(', ', $nomProcessVmMax) . ' autorise seulement une VM.');
            return $this->redirectToRoute('home');  
        }

        if(!empty($nomProcess)) {

            $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';
            $holidaysData = file_get_contents($holidaysFilePath);
            $holidays = json_decode($holidaysData, true);
    
            // Si la date de début tombe un week-end ou un jour fériés
            if ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')])) { // Si c'est un samedi, dimanche ou jour fériés
                // Avancer au prochain jour ouvrable (hors week-end et jours fériés)
                do {
                    $dateDebut->modify('+1 day');
                    // Vérifier si la date de début dépasse la date de fin
                    if ($dateDebut > $dateFin) {
                        // Ajouter un jour à la date de fin
                        $dateFin->modify('+1 day');
                    }
                } while ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')]));
            }
    
            $traitement->setDateDebut($dateDebut);
            $traitement->setDateFin($dateFin);
        }


        // Filtrer les processus en fonction du scénario
        $filteredProcesses = [];
        foreach ($selectedProcesses as $processId) {
            $process = $entityManager->getRepository(Process::class)->find($processId);
            foreach ($process->getIdScenario() as $scenario) {
                if ($scenario->getId() == $idScenario) {
                    $filteredProcesses[] = $process;
                }
            }
        }

        // Vérifier si $filteredProcesses est vide après la boucle
        if (empty($filteredProcesses)) {
            $this->addFlash('error', 'Process non trouvé.');
            return $this->redirectToRoute('home');  
        } else {
            foreach ($filteredProcesses as $process) {
                $traitement->addIdProcess($process);
            }
        }
        
        // Si des erreurs ont été détectées, afficher le message d'erreur
        if (!empty($vmNamesInError)) {
            $error_message = 'Les VM\'s suivantes sont déjà incluses dans un traitement pour la période spécifiée : ' . implode(', ', $vmNamesInError);
            $this->addFlash('error', $error_message);
            return $this->redirectToRoute('home');
        }

        if (stripos($traitement->getIdScenario()->getNomScenario(), 'puma') !== false) {
            $getEtape = $request->request->get('etape');

            $traitement->setEtape($getEtape);
        } else {
            $traitement->setEtape(null);
        }


        // Persister l'entité en base de données
        $entityManager->persist($traitement);
        $entityManager->flush();


        if (stripos($traitement->getIdScenario()->getNomScenario(), 'puma') === false) {
            // PERIODICITE
            $periodicity = $scenario->getPeriodicity();

            // Définir une date limite pour la reprogrammation
            $anneLimite = $traitement->getIdScenario()->getUntilWhen();
            $dateLimite = (new \DateTime())->modify('+' . $anneLimite . ' years');            

            while ($dateDebut < $dateLimite) {
                // Créer une copie des dates de début et de fin
                $nouvelleDateDebut = clone $dateDebut;
                $nouvelleDateFin = clone $dateFin;
            
                // Avancer la date de début et de fin selon la périodicité spécifiée
                if ($periodicity === "Hebdomadaire") {
                    $nouvelleDateDebut->modify('+7 days');
                    $nouvelleDateFin->modify('+7 days');
                } else if ($periodicity === "Mensuel") {
                    $nouvelleDateDebut->modify('+1 month');
                    $nouvelleDateFin->modify('+1 month');
                } else if ($periodicity === "Trimestriel") {
                    $nouvelleDateDebut->modify('+3 month');
                    $nouvelleDateFin->modify('+3 month');
                }
            
                // Vérifier si la nouvelle date de début est inférieure à la date limite
                if ($nouvelleDateDebut < $dateLimite) {
                    // Créer une nouvelle instance de traitement
                    $nouveauTraitement = clone $traitement;
                    $nouveauTraitement->setDateDebut($nouvelleDateDebut);
                    $nouveauTraitement->setDateFin($nouvelleDateFin);
            
                    // Persister le nouveau traitement en base de données
                    $entityManager->persist($nouveauTraitement);
                    $entityManager->flush();

                    // Création du message de log
                    $logMessage = 'Création d\'un traitement (n° ' .$nouveauTraitement->getId() . ') : 
                    Date de début : ' . $nouveauTraitement->getDateDebut()->format('d/m/Y à H:i:s') .  
                    ' Date de fin : ' . $traitement->getDateFin()->format('d/m/Y à H:i:s') .      
                    ' Caisse : ' . $nouveauTraitement->getIdCaisse()->getNomCaisse() . ' (' . $nouveauTraitement->getIdCaisse()->getNumCaisse() . ') ' .
                    ' Caisse exploitante : ' . $nouveauTraitement->getIdCaisseExploit()->getNomCaisse() . 
                    ' Pool : ' . $this->getPoolsNamesString($nouveauTraitement) . 
                    ' Scenario : ' . $nouveauTraitement->getIdScenario()->getNomScenario() . 
                    ' Nbre de dossier : ' . $nouveauTraitement->getNbreDossier() . 
                    ' VM associé : '  . $this->getVmNamesString($nouveauTraitement) . 
                    ' Process associé : '  . $this->getProcessNamesString($nouveauTraitement) . 
                    ' Périodicité : ' . $nouveauTraitement->getIdScenario()->getPeriodicity() . 
                    ' ' . $nouveauTraitement->getEtape();
        
                    // Création et enregistrement du log
                    $this->createLog($entityManager, $user, $logMessage);

                    // Mettre à jour les dates de début et de fin pour la prochaine itération
                    $dateDebut = $nouvelleDateDebut;
                    $dateFin = $nouvelleDateFin;
                } else {
                    // Si la nouvelle date de début est supérieure ou égale à la date limite, sortir de la boucle
                    break;
                }
            }
        }



        // Création du message de log
        $logMessage = 'Création d\'un traitement (n° ' .$traitement->getId() . ') : 
            Date de début : ' . $traitement->getDateDebut()->format('d/m/Y à H:i:s') .  
            ' Date de fin : ' . $traitement->getDateFin()->format('d/m/Y à H:i:s') .      
            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' .
            ' Caisse exploitante : ' . $traitement->getIdCaisseExploit()->getNomCaisse() . 
            ' Pool : ' . $this->getPoolsNamesString($traitement) . 
            ' Scenario : ' . $traitement->getIdScenario()->getNomScenario() . 
            ' Nbre de dossier : ' . $traitement->getNbreDossier() . 
            ' VM associé : '  . $this->getVmNamesString($traitement) . 
            ' Process associé : '  . $this->getProcessNamesString($traitement) . 
            ' ' . $traitement->getEtape();

        // Création et enregistrement du log
        $this->createLog($entityManager, $user, $logMessage);


        // REPLANIFICATION DES TRAITEMENTS POUR PUMA
        if (stripos($traitement->getIdScenario()->getNomScenario(), 'puma') !== false) {
            $parentTraitement = $traitement->getEtape();

            $dateDebutEtape2 = new \DateTime($request->request->get('date_debut_etape_2'));
            $dateFinEtape2 = new \DateTime($request->request->get('date_fin_etape_2'));

            $dateDebutEtape3 = new \DateTime($request->request->get('date_debut_etape_3'));
            $dateFinEtape3 = new \DateTime($request->request->get('date_fin_etape_3'));

            $dateDebutEtape4 = new \DateTime($request->request->get('date_debut_etape_4'));
            $dateFinEtape4 = new \DateTime($request->request->get('date_fin_etape_4'));

            $nbr_dossiers_etape_2 = $traitement->getNbreDossier(); // $request->request->get('nbr_dossiers_etape_2');
            $nbr_dossiers_etape_3 = $traitement->getNbreDossier();// $request->request->get('nbr_dossiers_etape_3');
            $nbr_dossiers_etape_4 = $traitement->getNbreDossier(); // $request->request->get('nbr_dossiers_etape_4');

            if ($parentTraitement === "Étape 1") {

                $secondTraitement = clone $traitement;
                $secondTraitement->setParentTraitement($traitement);    
                $this->createChildTreatment($user, $entityManager, $secondTraitement, $dateDebutEtape2, $dateFinEtape2, "Étape 2", $nbr_dossiers_etape_2);

                $thirdTraitement  = clone $traitement;
                $thirdTraitement->setParentTraitement($secondTraitement);
                $this->createChildTreatment($user, $entityManager, $thirdTraitement, $dateDebutEtape3, $dateFinEtape3, "Étape 3", $nbr_dossiers_etape_3);

                $fourthTraitement = clone $traitement;
                $fourthTraitement->setParentTraitement($thirdTraitement);
                $this->createChildTreatment($user, $entityManager, $fourthTraitement, $dateDebutEtape4, $dateFinEtape4, "Étape 4", $nbr_dossiers_etape_4);



            } else if ($parentTraitement === "Étape 2") {

                $thirdTraitement  = clone $traitement;
                $thirdTraitement->setParentTraitement($traitement);
                $this->createChildTreatment($user, $entityManager, $thirdTraitement, $dateDebutEtape3, $dateFinEtape3, "Étape 3", $nbr_dossiers_etape_3);

                $fourthTraitement = clone $traitement;
                $fourthTraitement->setParentTraitement($thirdTraitement);
                $this->createChildTreatment($user, $entityManager, $fourthTraitement, $dateDebutEtape4, $dateFinEtape4, "Étape 4", $nbr_dossiers_etape_4);



            } else if ($parentTraitement === "Étape 3") {
                $fourthTraitement = clone $traitement;
                $fourthTraitement->setParentTraitement($traitement);
                $this->createChildTreatment($user, $entityManager, $fourthTraitement, $dateDebutEtape4, $dateFinEtape4, "Étape 4", $nbr_dossiers_etape_4);
            }
        }
        

        // Vérifier le nombre total de VM pour les scénarios PUMA spécifiques après la replanification
        $dateTraitement = $traitement->getDateDebut(); // Utilisez la date appropriée ici
        $totalVmCountForPumaScenarios = $this->getTotalVmCountForPumaScenariosByDate($entityManager, $dateTraitement);

        // Limite maximale de VM autorisées
        $maxTotalVmCount = 120;

        if ($totalVmCountForPumaScenarios >= $maxTotalVmCount) {
            // Annuler la création du traitement
            $this->addFlash('error', 'La limite du nombre total de VM sur l\'ensemble scénarios PUMA sur cette période est atteinte (' . $maxTotalVmCount . '). Aucun nouveau traitement ne peut être créé durant cette période.');

            // Supprimer le traitement créé si nécessaire
            $entityManager->remove($traitement);
            $entityManager->flush(); 

            // Rediriger vers la page d'accueil ou une autre page
            return $this->redirectToRoute('home');
        }

        $this->addFlash('success', 'Traitement ajouté.');

        return $this->redirectToRoute('home');

    }










    private function createChildTreatment($user, $entityManager, $traitement, $dateDebut, $dateFin, $etape, $nbreDossier) {
    
        $processCollection = $traitement->getIdProcess();
        
        // Vérifiez chaque processus pour les week-ends et les jours fériés
        $weekendsAndHolidaysAllowed = false;
        foreach ($processCollection as $process) {
            if ($process->getWeekdsendsAndHolidays()) {
                $weekendsAndHolidaysAllowed = true;
                break; // Sortez de la boucle dès que vous trouvez un processus autorisant les week-ends et les jours fériés
            }
        }
        
        $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';
        $holidaysData = file_get_contents($holidaysFilePath);
        $holidays = json_decode($holidaysData, true);

        if ($weekendsAndHolidaysAllowed) {
            // Si la date de début tombe un week-end ou un jour fériés
            if ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')])) { // Si c'est un samedi, dimanche ou jour fériés
                // Avancer au prochain jour ouvrable (hors week-end et jours fériés)
                do {
                    $dateDebut->modify('+1 day');
                    // Vérifier si la date de début dépasse la date de fin
                    if ($dateDebut > $dateFin) {
                        // Ajouter un jour à la date de fin
                        $dateFin->modify('+1 day');
                    }
                } while ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')]));
            }
        }


        $traitement->setDateDebut($dateDebut);
        $traitement->setDateFin($dateFin);

        $traitement->setEtape($etape);

        if (empty($nbreDossier)) {
            $traitement->setNbreDossier('0');
        } else {
            $nbreDossier = intval($nbreDossier);
            $traitement->setNbreDossier($nbreDossier);
        }

        $entityManager->persist($traitement);
        $entityManager->flush();

        // Création du message de log
        $logMessage = 'Création d\'un traitement (n° ' .$traitement->getId() . ') : 
            Date de début : ' . $traitement->getDateDebut()->format('d/m/Y à H:i:s') .  
            ' Date de fin : ' . $traitement->getDateFin()->format('d/m/Y à H:i:s') .      
            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' .
            ' Caisse exploitante : ' . $traitement->getIdCaisseExploit()->getNomCaisse() . 
            ' Pool : ' . $this->getPoolsNamesString($traitement) . 
            ' Scenario : ' . $traitement->getIdScenario()->getNomScenario() . 
            ' Nbre de dossier : ' . $traitement->getNbreDossier() . 
            ' VM associé : '  . $this->getVmNamesString($traitement) . 
            ' Process associé : '  . $this->getProcessNamesString($traitement) . 
            ' ' . $traitement->getEtape();

        // Création et enregistrement du log
        $this->createLog($entityManager, $user, $logMessage);
    }































    public function deleteTraitment(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    { 
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);
    
        if (!$traitement) {
            $this->addFlash('error', 'Traitement non trouvé.');
            return $this->redirectToRoute('setting');  
        }
        
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

        $this->deleteRecursive($user, $entityManager, $traitement);

        $this->addFlash('success', 'Traitement supprimé.');

        return $this->redirectToRoute('setting');  
    }
    
    
 


    public function deleteTraitmentExtern(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    { 
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);
    
        if (!$traitement) {
            $this->addFlash('error', 'Traitement non trouvé.');
            return $this->redirectToRoute('setting');  
        }
        
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

       $caisseId = $traitement->getIdCaisse();

        $this->deleteRecursive($user, $entityManager, $traitement);

        $this->addFlash('success', 'Traitement supprimé.');

        return $this->redirectToRoute('info_caisse', ['id' => $caisseId]);
    }
    

    private function deleteRecursive(UserInterface $user, EntityManagerInterface $entityManager, Treatment $traitement): void
    {

        // SUPPRESSIONS DES TRAITEMENTS POUR PUMA
        if (stripos($traitement->getIdScenario()->getNomScenario(), 'puma') !== false) {
            $replanifications = $traitement->getReplanifications();
        
            foreach ($replanifications as $replanification) {
                $this->deleteRecursive($user, $entityManager, $replanification);
            }
        }
    
        // Create log message
        $logMessage = 'Suppression d\'un traitement (n° ' .$traitement->getId() . ') : 
            Date de début : ' . $traitement->getDateDebut()->format('d/m/Y à H:i:s') .  
            ' Date de fin : ' . $traitement->getDateFin()->format('d/m/Y à H:i:s') .      
            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' .
            ' Caisse exploitante : ' . $traitement->getIdCaisseExploit()->getNomCaisse() . 
            ' Pool : ' . $this->getPoolsNamesString($traitement) . 
            ' Scenario : ' . $traitement->getIdScenario()->getNomScenario() . 
            ' Nbre de dossier : ' . $traitement->getNbreDossier() . 
            ' VM associé : '  . $this->getVmNamesString($traitement) . 
            ' Process associé : '  . $this->getProcessNamesString($traitement) . 
            ' ' . $traitement->getEtape();
    
        // Create and save log
        $this->createLog($entityManager, $user, $logMessage);

        // Remove treatment
        $entityManager->remove($traitement);
        $entityManager->flush();
    }





































    











    public function editTraitment ($id, UserInterface $user, EntityManagerInterface $entityManager, Request $request): Response
    { 
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

        $pools = $traitement->getIdPool();
        foreach ($pools as $pool) {
            $traitement->removeIdPool($pool);
            $pool->removeIdTraitement($traitement);
        }

        $vms = $traitement->getIdVm();
        foreach ($vms as $vm) {
            $traitement->removeIdVm($vm);
            $vm->removeIdTraitement($traitement);
        }

        $processes = $traitement->getIdProcess();
        foreach ($processes as $process) {
            $traitement->removeIdProcess($process);
            $process->removeIdTraitement($traitement);
        }


        $idCaisse = $request->request->get('newCaisse');
        $idCaisseExploit = $request->request->get('newCaisseExploit');

        $selectedPool = $request->request->get('newPool', []);

        $idScenario = $request->request->get('newScenario');

        $selectedVMs = $request->request->get('newVm', []);
        $selectedProcesses = $request->request->get('newProcess', []);

        $nbreDossier = $request->request->get('newNbredossier');

        $dateDebut = new \DateTime($request->request->get('newDatedebut'));
        $dateFin = new \DateTime($request->request->get('newDatefin'));

        $traitement->setDateDebut($dateDebut);
        $traitement->setDateFin($dateFin);

        // Obtenez les entités Caisse et PoolVm à partir de leur ID (suppose que ces entités existent)
        $caisse = $entityManager->getRepository(Caisse::class)->find($idCaisse);
        $caisseExploit = $entityManager->getRepository(CaisseExploit::class)->find($idCaisseExploit);
        $scenario = $entityManager->getRepository(Scenario::class)->find($idScenario);

        if (empty($nbreDossier)) {
            $traitement->setNbreDossier('0');
        } else {
            $nbreDossier = intval($nbreDossier);
            $traitement->setNbreDossier($nbreDossier);
        }

        if (!$caisse) {
            $this->addFlash('error', 'Caisse non trouvé.');
            return $this->redirectToRoute('home');
        } $traitement->setIdCaisse($caisse);

        if (!$caisseExploit) {
            $this->addFlash('error', 'Caisse exploitante non trouvé.');
            return $this->redirectToRoute('home');
        } $traitement->setIdCaisseExploit($caisseExploit);

        if (!$scenario) {
            $this->addFlash('error', 'Scenario non trouvé.');
            return $this->redirectToRoute('home');
        } $traitement->setIdScenario($scenario);

        if (!is_array($selectedPool)) {
            $this->addFlash('error', 'Sélection de pool invalide.');
            return $this->redirectToRoute('home');
        }

        if (!is_array($selectedProcesses)) {
            $this->addFlash('error', 'Sélection de process invalide.');
            return $this->redirectToRoute('home');
        }

        if (!is_array($selectedVMs)) {
            $this->addFlash('error', 'Sélection de vm invalide.');
            return $this->redirectToRoute('home');
        }
        
        $filteredPool = [];
        foreach ($selectedPool as $poolId) {
            if ($poolId !== 'Tous') {
                $pool = $entityManager->getRepository(PoolVm::class)->find($poolId);
                $filteredPool[] = $pool;
            }
        }
        
        if (empty($filteredPool)) {
            $this->addFlash('error', 'Pool non trouvé.');
            return $this->redirectToRoute('home');  
        } else {
            foreach ($filteredPool as $pool) {
                $traitement->addIdPool($pool);
                $pool->addIdTraitement($traitement);
            }
        }


        // Filtrer les VM en fonction de la pool
        $filteredVMs = [];
        $vmNamesInError = [];
        foreach ($selectedVMs as $vmId) {
            $vm = $entityManager->getRepository(Vm::class)->find($vmId);

            // Vérifier si la VM est déjà incluse dans un traitement pendant la période spécifiée
            $existingTraitement = $entityManager->getRepository(Treatment::class)->findTraitementForVmAndDateRange($vm, $dateDebut, $dateFin);

            if ($existingTraitement) {
                // Ajouter le nom de la VM à la liste d'erreurs
                $vmNamesInError[] = $vm->getNomVm();
            }

            if ($vm && in_array($vm->getPoolVm()->getId(), $selectedPool)) {
                $filteredVMs[] = $vm;
            }
        }

        // Si des erreurs ont été détectées, afficher le message d'erreur
        if (!empty($vmNamesInError)) {
            $error_message = 'Les VM\'s suivantes sont déjà incluses dans un traitement pour la période spécifiée : ' . implode(', ', $vmNamesInError);
            $this->addFlash('error', $error_message);
            return $this->redirectToRoute('home');
        }

        if (empty($filteredVMs)) {
            $this->addFlash('error', 'VM non trouvé.');
            return $this->redirectToRoute('home');  
        } else {
            foreach ($filteredVMs as $vm) {
                $traitement->addIdVm($vm);
                $vm->addIdTraitement($traitement);
            }
        }

        $nomProcessVmMax = [];
        $nomProcess = [];

        foreach ($selectedProcesses as $processId) {
            $process = $entityManager->getRepository(Process::class)->find($processId);
        
            // Ajoutez la vérification de l'attribut vm_max ici
            if ($process && $process->getVmMax() && count($filteredVMs) > 1) {
                $nomProcessVmMax[] = $process->getNomProcess();
            }

            if ($process && $process->getWeekdsendsAndHolidays()) {

                if ($dateDebut->format('N') >= 6 || $dateFin->format('N') >= 6) {
                    $nomProcess[] = $process->getNomProcess();
                }

                $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';
                $holidaysData = file_get_contents($holidaysFilePath);
                $holidays = json_decode($holidaysData, true);

                $traitementDates = [$traitement->getDateDebut()->format('Y-m-d'), $traitement->getDateFin()->format('Y-m-d')];
                foreach ($traitementDates as $date) {
                    if (array_key_exists($date, $holidays)) {
                        $nomProcess[] = $process->getNomProcess();
                    }
                }
            }
        }

        if(!empty($nomProcessVmMax)) {
            $this->addFlash('error', 'Le processus ' . implode(', ', $nomProcessVmMax) . ' autorise seulement une VM.');
            return $this->redirectToRoute('home');  
        }

        if(!empty($nomProcess)) {

            $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';
            $holidaysData = file_get_contents($holidaysFilePath);
            $holidays = json_decode($holidaysData, true);
    
            // Si la date de début tombe un week-end ou un jour fériés
            if ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')])) { // Si c'est un samedi, dimanche ou jour fériés
                // Avancer au prochain jour ouvrable (hors week-end et jours fériés)
                do {
                    $dateDebut->modify('+1 day');
                    // Vérifier si la date de début dépasse la date de fin
                    if ($dateDebut > $dateFin) {
                        // Ajouter un jour à la date de fin
                        $dateFin->modify('+1 day');
                    }
                } while ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')]));
            }
    
            $traitement->setDateDebut($dateDebut);
            $traitement->setDateFin($dateFin);
        }


        // Filtrer les processus en fonction du scénario
        $filteredProcesses = [];
        foreach ($selectedProcesses as $processId) {
            $process = $entityManager->getRepository(Process::class)->find($processId);
            foreach ($process->getIdScenario() as $scenario) {
                if ($scenario->getId() == $idScenario) {
                    $filteredProcesses[] = $process;
                }
            }
        }

        // Vérifier si $filteredProcesses est vide après la boucle
        if (empty($filteredProcesses)) {
            $this->addFlash('error', 'Process non trouvé.');
            return $this->redirectToRoute('home');  
        } else {
            foreach ($filteredProcesses as $process) {
                $traitement->addIdProcess($process);
            }
        }

        if (stripos($traitement->getIdScenario()->getNomScenario(), 'puma') !== false) {
            $getEtape = $request->request->get('newEtape');

            $traitement->setEtape($getEtape);
        } else {
            $traitement->setEtape(null);
        }



        // Persister l'entité en base de données
        $entityManager->persist($traitement);


        // Vérifier le nombre total de VM pour les scénarios PUMA
        $dateTraitement = $traitement->getDateDebut(); // Utilisez la date appropriée ici
        $totalVmCountForPumaScenarios = $this->getTotalVmCountForPumaScenariosByDate($entityManager, $dateTraitement);

        // Limite maximale de VM autorisées
        $maxTotalVmCount = 120;

        if ($totalVmCountForPumaScenarios >= $maxTotalVmCount) {
            // Annuler la création du traitement
            $this->addFlash('error', 'La limite du nombre total de VM sur l\'ensemble scénarios PUMA sur cette période est atteinte (' . $maxTotalVmCount . '). Aucune vm ne peut être ajouté dans un traitement durant cette période.');

            // Rediriger vers la page d'accueil ou une autre page
            return $this->redirectToRoute('home');
        }

        // Création du message de log
        $logMessage = 'Modification d\'un traitement (n° ' .$traitement->getId() . ') : 
            Date de début : ' . $traitement->getDateDebut()->format('d/m/Y à H:i:s') .  
            ' Date de fin : ' . $traitement->getDateFin()->format('d/m/Y à H:i:s') .      
            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' .
            ' Caisse exploitante : ' . $traitement->getIdCaisseExploit()->getNomCaisse() . 
            ' Pool : ' . $this->getPoolsNamesString($traitement) . 
            ' Scenario : ' . $traitement->getIdScenario()->getNomScenario() . 
            ' Nbre de dossier : ' . $traitement->getNbreDossier() . 
            ' VM associé : '  . $this->getVmNamesString($traitement) . 
            ' Process associé : '  . $this->getProcessNamesString($traitement) . 
            ' ' . $traitement->getEtape();

        // Création et enregistrement du log
        $this->createLog($entityManager, $user, $logMessage);




        // REPLANIFICATION DES TRAITEMENTS POUR PUMA
        if (stripos($traitement->getIdScenario()->getNomScenario(), 'puma') !== false) {

            $childTraitements = $traitement->getReplanifications();
    
            foreach ($childTraitements as $childTraitement) {
                $this->editChildTreatmentRecursive($user, $entityManager, $traitement, $childTraitement);
            }

        }

        $this->addFlash('success', 'Traitement modifié.');

        return $this->redirectToRoute('home');
    }


    private function editChildTreatmentRecursive($user, $entityManager, $parentTraitement, $childTraitement)
    {   
        $pools = $childTraitement->getIdPool();
        foreach ($pools as $pool) {
            $childTraitement->removeIdPool($pool);
            $pool->removeIdTraitement($childTraitement);
        }

        $vms = $childTraitement->getIdVm();
        foreach ($vms as $vm) {
            $childTraitement->removeIdVm($vm);
            $vm->removeIdTraitement($childTraitement);
        }

        $processes = $childTraitement->getIdProcess();
        foreach ($processes as $process) {
            $childTraitement->removeIdProcess($process);
            $process->removeIdTraitement($childTraitement);
        }

        $nbreDossier = $parentTraitement->getNbreDossier();
        $dateDebut = $parentTraitement->getDateFin();

        if (empty($nbreDossier)) {
            $childTraitement->setNbreDossier('0');
        } else {
            $nbreDossier = intval($nbreDossier);
            $childTraitement->setNbreDossier($nbreDossier);
        }

        $childTraitement->setIdScenario($parentTraitement->getIdScenario());

        foreach ($parentTraitement->getIdPool() as $pool) {
            $childTraitement->addIdPool($pool);
        }

        foreach ($parentTraitement->getIdVm() as $vm) {
            $childTraitement->addIdVm($vm);
        }

        foreach ($parentTraitement->getIdProcess() as $process) {
            $childTraitement->addIdProcess($process);
        }

        $delai1 = $parentTraitement->getIdScenario()->getDelai1();
        $delai2 = $parentTraitement->getIdScenario()->getDelai2();
        $delai3 = $parentTraitement->getIdScenario()->getDelai3();
        
        $dureeTraitement = $parentTraitement->getDateFin()->getTimestamp() - $parentTraitement->getDateDebut()->getTimestamp();
        
        if ($childTraitement->getEtape() === "Étape 2") {
            $dateDebut->modify("+$delai1 days");
            $dateFin = clone $dateDebut;
            $dateFin->add(new \DateInterval('PT' . $dureeTraitement . 'S'));  
        } 
        else if ($childTraitement->getEtape() === "Étape 3") {
            $dateDebut->modify("+$delai2 days");
            $dateFin = clone $dateDebut;
            $dateFin->add(new \DateInterval('PT' . $dureeTraitement . 'S'));  
        } 
        else if ($childTraitement->getEtape() === "Étape 4") {
            $dateDebut->modify("+$delai3 days");
            $dateFin = clone $dateDebut;
            $dateFin->add(new \DateInterval('PT' . $dureeTraitement . 'S'));  
        }
                


        $processCollection = $childTraitement->getIdProcess();

        // Vérifiez chaque processus pour les week-ends et les jours fériés
        $weekendsAndHolidaysAllowed = false;
        foreach ($processCollection as $process) {
            if ($process->getWeekdsendsAndHolidays()) {
                $weekendsAndHolidaysAllowed = true;
                break; // Sortez de la boucle dès que vous trouvez un processus autorisant les week-ends et les jours fériés
            }
        }
        
        $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';
        $holidaysData = file_get_contents($holidaysFilePath);
        $holidays = json_decode($holidaysData, true);

        if ($weekendsAndHolidaysAllowed) {
            // Si la date de début tombe un week-end ou un jour fériés
            if ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')])) { // Si c'est un samedi, dimanche ou jour fériés
                // Avancer au prochain jour ouvrable (hors week-end et jours fériés)
                do {
                    $dateDebut->modify('+1 day');
                    // Vérifier si la date de début dépasse la date de fin
                    if ($dateDebut > $dateFin) {
                        // Ajouter un jour à la date de fin
                        $dateFin->modify('+1 day');
                    }
                } while ($dateDebut->format('N') >= 6 || isset($holidays[$dateDebut->format('Y-m-d')]));
            }
        }

        $childTraitement->setDateDebut($dateDebut);
        $childTraitement->setDateFin($dateFin);

        $entityManager->persist($childTraitement);
        $entityManager->flush($childTraitement);

        // Création du message de log
        $logMessage = 'Modification d\'un traitement (n° ' .$childTraitement->getId() . ') : 
            Date de début : ' . $childTraitement->getDateDebut()->format('d/m/Y à H:i:s') .  
            ' Date de fin : ' . $childTraitement->getDateFin()->format('d/m/Y à H:i:s') .      
            ' Caisse : ' . $childTraitement->getIdCaisse()->getNomCaisse() . ' (' . $childTraitement->getIdCaisse()->getNumCaisse() . ') ' .
            ' Caisse exploitante : ' . $childTraitement->getIdCaisseExploit()->getNomCaisse() . 
            ' Pool : ' . $this->getPoolsNamesString($childTraitement) . 
            ' Scenario : ' . $childTraitement->getIdScenario()->getNomScenario() . 
            ' Nbre de dossier : ' . $childTraitement->getNbreDossier() . 
            ' VM associé : '  . $this->getVmNamesString($childTraitement) . 
            ' Process associé : '  . $this->getProcessNamesString($childTraitement) . 
            ' ' . $childTraitement->getEtape();

        // Création et enregistrement du log
        $this->createLog($entityManager, $user, $logMessage);


        
        $childReplanifications = $childTraitement->getReplanifications();
        foreach ($childReplanifications as $childReplanification) {
            // Appel récursif
            $this->editChildTreatmentRecursive($user, $entityManager, $childTraitement, $childReplanification);
        }
    }




























































































    public function all_traitments (CaisseExploitRepository $CaisseExploitRepository, CaisseRepository $CaisseRepository, ScenarioRepository $ScenarioRepository, PoolVmRepository $PoolVmRepository, UserInterface $user): Response
    { 
        $listAllCaisseExploit = $CaisseExploitRepository->findAll();
        $listAllCaisse = $CaisseRepository->findAll();
        $listAllPoolVM = $PoolVmRepository->findAll();
        $listAllScenario = $ScenarioRepository->findAll();

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('traitement/all.traitement.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'listAllCaisseExploit' => $listAllCaisseExploit,
            'listAllCaisse' => $listAllCaisse,
            'listAllPoolVM' => $listAllPoolVM,
            'listAllScenario' => $listAllScenario,
        ]);
    }





    public function modifyAllTraitments(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    { 
        $idCaisseExploit = $request->request->get('caisse_exploit');
        $idPool = $request->request->get('pool');
        $day = $request->request->get('day');
        $hour = $request->request->get('hour');
        $min = $request->request->get('min');

        $dateString = $request->request->get('date');
        
        $caisseExploit = $entityManager->getRepository(CaisseExploit::class)->find($idCaisseExploit);

        if (!$caisseExploit) {
            $this->addFlash('error', 'Aucune caisse exploitante trouvée.');
            return $this->redirectToRoute('all_traitments');
        }

        // Vérifier si des traitements existent pour cette caisse exploitante
        $nbTraitements = $entityManager->getRepository(Treatment::class)->count(['id_caisse_exploit' => $idCaisseExploit]);

        if ($nbTraitements === 0) {
            $this->addFlash('error', 'Aucun traitement trouvé pour cette caisse exploitante.');
            return $this->redirectToRoute('all_traitments');
        }

        // Vérifier si au moins un des champs jour, heure ou minute est rempli
        if ($day || $hour || $min) {

            if ($idPool) {
                $pool = $entityManager->getRepository(PoolVm::class)->find($idPool);

                if (!$pool) {
                    $this->addFlash('error', 'La pool spécifiée n\'existe pas.');
                    return $this->redirectToRoute('all_traitments');
                }

                $traitements = $pool->getIdTraitement();

                if ($traitements->isEmpty()) {
                    $this->addFlash('error', 'Aucun traitement trouvé pour cette pool.');
                    return $this->redirectToRoute('all_traitments');
                }

                if ($traitements) {
                    foreach ($traitements as $traitement) {
                        if ($traitement->getEtat() === "En cours") {
                            $dateDebut = clone $traitement->getDateDebut();
                            $dateFin = clone $traitement->getDateFin();

                            $intervalSpec = "P";
                            if ($day) {
                                $intervalSpec .= abs($day) . "D"; // Utilisez abs() pour obtenir la valeur absolue
                            }
                            if ($hour || $min) {
                                $intervalSpec .= "T";
                            }
                            if ($hour) {
                                $intervalSpec .= abs($hour) . "H"; // Utilisez abs() pour obtenir la valeur absolue
                            }
                            if ($min) {
                                $intervalSpec .= abs($min) . "M"; // Utilisez abs() pour obtenir la valeur absolue
                            }
                            
                            $interval = new \DateInterval($intervalSpec);

                            // Vérifiez si les valeurs sont négatives et ajustez-les en conséquence
                            if ($day < 0 || $hour < 0 || $min < 0) {
                                $dateDebut->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                                $dateFin->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                            } else {
                                $dateDebut->add($interval);
                                $dateFin->add($interval);
                            }

                            // Appliquer les nouvelles dates de début et de fin au traitement
                            $traitement->setDateDebut($dateDebut);
                            $traitement->setDateFin($dateFin);

                            $entityManager->persist($traitement);
                        } else {
                            $this->addFlash('error', 'Aucun traitement "En cours" trouvé dans cette pool.');
                            return $this->redirectToRoute('all_traitments');
                        }
                    }
                } else {
                    $this->addFlash('error', 'Aucun traitement trouvé dans cette pool.');
                    return $this->redirectToRoute('all_traitments');
                }
            } 





            else if ($dateString) {
                $date = new \DateTime($dateString);

                if ($idPool) {
                    $pool = $entityManager->getRepository(PoolVm::class)->find($idPool);
    
                    if (!$pool) {
                        $this->addFlash('error', 'La pool spécifiée n\'existe pas.');
                        return $this->redirectToRoute('all_traitments');
                    }
    
                    $traitements = $pool->getIdTraitement();
    
                    if ($traitements->isEmpty()) {
                        $this->addFlash('error', 'Aucun traitement trouvé pour cette pool.');
                        return $this->redirectToRoute('all_traitments');
                    }
    
                    if ($traitements) {
                        foreach ($traitements as $traitement) {
                            if ($traitement->getEtat() === "En cours" && $traitement->getDateDebut() >= $date) {
                                $dateDebut = clone $traitement->getDateDebut();
                                $dateFin = clone $traitement->getDateFin();
    
                                $intervalSpec = "P";
                                if ($day) {
                                    $intervalSpec .= abs($day) . "D"; // Utilisez abs() pour obtenir la valeur absolue
                                }
                                if ($hour || $min) {
                                    $intervalSpec .= "T";
                                }
                                if ($hour) {
                                    $intervalSpec .= abs($hour) . "H"; // Utilisez abs() pour obtenir la valeur absolue
                                }
                                if ($min) {
                                    $intervalSpec .= abs($min) . "M"; // Utilisez abs() pour obtenir la valeur absolue
                                }
                                
                                $interval = new \DateInterval($intervalSpec);
    
                                // Vérifiez si les valeurs sont négatives et ajustez-les en conséquence
                                if ($day < 0 || $hour < 0 || $min < 0) {
                                    $dateDebut->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                                    $dateFin->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                                } else {
                                    $dateDebut->add($interval);
                                    $dateFin->add($interval);
                                }
    
                                // Appliquer les nouvelles dates de début et de fin au traitement
                                $traitement->setDateDebut($dateDebut);
                                $traitement->setDateFin($dateFin);
    
                                $entityManager->persist($traitement);
                            } else {
                                $this->addFlash('error', 'Aucun traitement "En cours" trouvé à partir de cette date.');
                                return $this->redirectToRoute('all_traitments');
                            }
                        }
                    } else {
                        $this->addFlash('error', 'Aucun traitement trouvé à partir de cette date.');
                        return $this->redirectToRoute('all_traitments');
                    }
                } else {
                    $traitements = $entityManager->getRepository(Treatment::class)->findBy(['id_caisse_exploit' => $idCaisseExploit]);

                    if ($traitements) {
                        foreach ($traitements as $traitement) {
                            if ($traitement->getEtat() === "En cours" && $traitement->getDateDebut() >= $date) {
                                $dateDebut = clone $traitement->getDateDebut();
                                $dateFin = clone $traitement->getDateFin();
    
                                $intervalSpec = "P";
                                if ($day) {
                                    $intervalSpec .= abs($day) . "D"; // Utilisez abs() pour obtenir la valeur absolue
                                }
                                if ($hour || $min) {
                                    $intervalSpec .= "T";
                                }
                                if ($hour) {
                                    $intervalSpec .= abs($hour) . "H"; // Utilisez abs() pour obtenir la valeur absolue
                                }
                                if ($min) {
                                    $intervalSpec .= abs($min) . "M"; // Utilisez abs() pour obtenir la valeur absolue
                                }
                                
                                $interval = new \DateInterval($intervalSpec);
    
                                // Vérifiez si les valeurs sont négatives et ajustez-les en conséquence
                                if ($day < 0 || $hour < 0 || $min < 0) {
                                    $dateDebut->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                                    $dateFin->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                                } else {
                                    $dateDebut->add($interval);
                                    $dateFin->add($interval);
                                }
    
                                // Appliquer les nouvelles dates de début et de fin au traitement
                                $traitement->setDateDebut($dateDebut);
                                $traitement->setDateFin($dateFin);
    
                                $entityManager->persist($traitement);
                            } else {
                                $this->addFlash('error', 'Aucun traitement "En cours" trouvé à partir de cette date.');
                                return $this->redirectToRoute('all_traitments');
                            }
                        }
                    } else {
                        $this->addFlash('error', 'Aucun traitement trouvé à partir de cette date.');
                        return $this->redirectToRoute('all_traitments');
                    }
                }
            }
            










            
            else {
                $traitements = $entityManager->getRepository(Treatment::class)->findBy(['id_caisse_exploit' => $idCaisseExploit]);

                if ($traitements) {
                    foreach ($traitements as $traitement) {
                        if ($traitement->getEtat() === "En cours") {
                            $dateDebut = clone $traitement->getDateDebut();
                            $dateFin = clone $traitement->getDateFin();

                            $intervalSpec = "P";
                            if ($day) {
                                $intervalSpec .= abs($day) . "D"; // Utilisez abs() pour obtenir la valeur absolue
                            }
                            if ($hour || $min) {
                                $intervalSpec .= "T";
                            }
                            if ($hour) {
                                $intervalSpec .= abs($hour) . "H"; // Utilisez abs() pour obtenir la valeur absolue
                            }
                            if ($min) {
                                $intervalSpec .= abs($min) . "M"; // Utilisez abs() pour obtenir la valeur absolue
                            }
                            
                            $interval = new \DateInterval($intervalSpec);

                            // Vérifiez si les valeurs sont négatives et ajustez-les en conséquence
                            if ($day < 0 || $hour < 0 || $min < 0) {
                                $dateDebut->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                                $dateFin->sub($interval); // Utilisez sub() pour soustraire l'intervalle
                            } else {
                                $dateDebut->add($interval);
                                $dateFin->add($interval);
                            }

                            // Appliquer les nouvelles dates de début et de fin au traitement
                            $traitement->setDateDebut($dateDebut);
                            $traitement->setDateFin($dateFin);

                            $entityManager->persist($traitement);
                        } else {
                            $this->addFlash('error', 'Aucun traitement "En cours" trouvé.');
                            return $this->redirectToRoute('all_traitments');
                        }
                    }
                } else {
                    $this->addFlash('error', 'Aucun traitement trouvé pour cette caisse exploitante.');
                    return $this->redirectToRoute('all_traitments');
                }
            }

            $entityManager->flush();

            $day = $day ?: 0;
            $hour = $hour ?: 0;
            $min = $min ?: 0;
            $poolName = isset($pool) ? " et de la pool : " . $pool->getNomPool() : "et de toutes les pools, ";
            $aPartirDe = isset($date) ? " à partir du  : " . $date->format('d/m/Y H:i:s') : " ";

            // Création du message de log
            $logMessage = 'Modification de tous les traitements de la caisse exploitante : ' . $caisseExploit->getNomCaisse() . 
                        ' ' . $poolName .
                        ' ' . $aPartirDe .
                        ' Décalage de : ' . $day . ' jours ' . $hour . ' heures ' .  $min . ' minutes ';

            // Création et enregistrement du log
            $this->createLog($entityManager, $user, $logMessage);


            $this->addFlash('success', 'Tous les traitements ont été modifiés.');
        } else {
            $this->addFlash('error', 'Aucune modification apportée car aucun décalage n\'a été spécifié.');
        }
                                
        return $this->redirectToRoute('all_traitments');
    }














    public function deleteAllTraitments(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {              
        $deleteOption = $request->request->get('deleteOption');
        $date1 = $request->request->get('date1'); // Retrieve the optional date from the request
        $traitments = [];
        $filterName = '';
    
        // Récupérer la valeur du filtre selon l'option sélectionnée
        switch ($deleteOption) {
            case 'caisse':
                $filterId = $request->request->get('filterValueCaisse');
                $caisse = $entityManager->getRepository(Caisse::class)->find($filterId);
                if ($caisse) {
                    $filterName = $caisse->getNomCaisse(); // Récupérer le nom de la caisse
                }
                $queryBuilder = $entityManager->getRepository(Treatment::class)->createQueryBuilder('t')
                    ->where('t.id_caisse_exploit = :filterId')
                    ->setParameter('filterId', $filterId);
                break;
            case 'pool':
                $filterId = $request->request->get('filterValuePool');
                $pool = $entityManager->getRepository(PoolVm::class)->find($filterId);
                if ($pool) {
                    $filterName = $pool->getNomPool(); // Récupérer le nom du pool
                }
                $queryBuilder = $entityManager->getRepository(Treatment::class)->createQueryBuilder('t')
                    ->join('t.id_pool', 'p')
                    ->where('p.id = :filterId')
                    ->setParameter('filterId', $filterId);
                break;
            case 'scenario':
                $filterId = $request->request->get('filterValueScenario');
                $scenario = $entityManager->getRepository(Scenario::class)->find($filterId);
                if ($scenario) {
                    $filterName = $scenario->getNomScenario(); // Récupérer le nom du scénario
                }
                $queryBuilder = $entityManager->getRepository(Treatment::class)->createQueryBuilder('t')
                    ->where('t.id_scenario = :filterId')
                    ->setParameter('filterId', $filterId);
                break;
            case 'periodique':
                $filterPeriod = $request->request->get('filterValuePeriod');
                if ($filterPeriod) {
                    $filterName = $filterPeriod; // Récupérer la périodicité
                    $scenarios = $entityManager->getRepository(Scenario::class)->findBy(['periodicity' => $filterPeriod]);
                    $scenarioIds = array_map(fn($s) => $s->getId(), $scenarios);
                    $queryBuilder = $entityManager->getRepository(Treatment::class)->createQueryBuilder('t')
                        ->where('t.id_scenario IN (:scenarioIds)')
                        ->setParameter('scenarioIds', $scenarioIds);
                }
                break;
            default:
                $this->addFlash('error', 'Option de suppression invalide.');
                return $this->redirectToRoute('all_traitments');
        }
    
        // If a date is provided, add it to the query
        if ($date1) {
            $queryBuilder->andWhere('t.date_debut >= :date1')
                ->setParameter('date1', new \DateTime($date1));
        }
    
        // Execute the query to get the treatments
        $traitments = $queryBuilder->getQuery()->getResult();
    
        // Supprimer les traitements associés
        if ($traitments) {
            foreach ($traitments as $traitement) {
                $entityManager->remove($traitement);
            }
    
            // Appliquer les suppressions
            $entityManager->flush();
    
            // Création du message de log
            $logMessage = 'Suppression de tous les traitements contenant le filtre : ' . $filterName;
            if ($date1) {
                $formattedDate = (new \DateTime($date1))->format('d/m/Y \à H:i:s');
                $logMessage .= ' à partir du ' . $formattedDate;
            }
    
            // Création et enregistrement du log
            $this->createLog($entityManager, $user, $logMessage);
    
            $this->addFlash('success', 'Les traitements ont été supprimés avec succès.');
        } else {
            $this->addFlash('error', 'Aucun traitement trouvé pour la valeur sélectionnée.');
        }
    
        return $this->redirectToRoute('all_traitments');
    }
    
    












































































    public function generatePdf (EntityManagerInterface $entityManager, $id): Response
    { 
        
        $caisse = $entityManager->getRepository(Caisse::class)->find($id);

        if (!$caisse) {
            $this->addFlash('error', 'Aucune caisse trouvé.');
            return $this->redirectToRoute('info_caisse', ['id' => $id]);
        }

        $traitements = $entityManager->getRepository(Treatment::class)->findBy(['id_caisse' => $caisse]);

        if (empty($traitements)) {
            $this->addFlash('error', 'Aucun traitement trouvé.');
            return $this->redirectToRoute('info_caisse', ['id' => $id]);
        }

        $dateDuJour = new \DateTime();
        $dateDuJourFormatted = $dateDuJour->format('d/m/Y');
        $pdfTitle = 'rapport_caisse_' . $caisse->getNomCaisse() . '_(' . $caisse->getNumCaisse() . ')_' .$dateDuJourFormatted . '.pdf';

        $html = '<style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                </style>';
        $html .= '<h1>Rapport des traitements pour la caisse ' . $caisse->getNomCaisse() . ' (' . $caisse->getNumCaisse() .') du ' . $dateDuJourFormatted . '</h1>';
        $html .= '<table>';
        $html .= '<tr><th>Date Début</th><th>Date Fin</th><th>Caisse exploitante</th><th>Scénario</th><th>Étape</th><th>Commentaire d\'erreur</th><th>Commentaire de clôture</th><th>Nombre de Dossiers</th><th>État</th></tr>';
        foreach ($traitements as $traitement) {
        $html .= '<tr>';
        $html .= '<td>' . $traitement->getDateDebut()->format('d/m/Y à H:i:s') . '</td>';
        $html .= '<td>' . $traitement->getDateFin()->format('d/m/Y à H:i:s') . '</td>';
        $html .= '<td>' . $traitement->getIdCaisseExploit()->getNomCaisse() . ' (' . $traitement->getIdCaisseExploit()->getNumCaisse(). ') ' . '</td>';
        $html .= '<td>' . $traitement->getIdScenario()->getNomScenario() . '</td>';
        $html .= '<td>' . $traitement->getEtape() . '</td>';
        $html .= '<td>' . $traitement->getErrorComment() . '</td>';
        $html .= '<td>' . $traitement->getClotureComment() . '</td>';
        $html .= '<td>' . $traitement->getNbreDossier() . '</td>';
        $html .= '<td>' . $traitement->getEtat() . '</td>';
        $html .= '</tr>';
        }
        $html .= '</table>';


        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        $dompdf->render();

        // Return the PDF as a response
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $pdfTitle . '"'
            ]
        );
    }
















































    private function getVmNamesString(Treatment $traitement): string
    {
        $vmNames = [];
        foreach ($traitement->getIdVm() as $vm) {
            $vmNames[] = $vm->getNomVm();
        }
    
        return implode(', ', $vmNames);
    }
    

    private function getPoolsNamesString(Treatment $traitement): string
    {
        $poolNames = [];
        foreach ($traitement->getIdPool() as $pool) {
            $poolNames[] = $pool->getNomPool();
        }
    
        return implode(', ', $poolNames);
    }



    private function getProcessNamesString(Treatment $traitement): string
    {
        $processNames = [];
        foreach ($traitement->getIdProcess() as $process) {
            $processNames[] = $process->getNomProcess();
        }
    
        return implode(', ', $processNames);
    }


    private function createLog($entityManager, $user, $message)
    {
        // Création d'une instance de Logs
        $log = new Logs();
        $log->setAuteur($user->getNom() . ' ' . $user->getPrenom());
        $log->setDate(new \DateTime());
        $log->setAction($message);
    
        // Enregistrement du log
        $entityManager->persist($log);
        $entityManager->flush();
    }
}





