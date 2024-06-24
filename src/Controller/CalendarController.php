<?php

namespace App\Controller;


use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Treatment;
use App\Entity\PoolVm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Logs;
use App\Entity\Message;
use App\Entity\CaisseExploit;

class CalendarController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }

    public function getAnnoncesEvents(EntityManagerInterface $entityManager): JsonResponse
    {
        $annonces = $entityManager->getRepository(Message::class)->findAll();

        $events = [];

        foreach ($annonces as $annonce) {

            $event = [
                'id' => $annonce->getId(),
                'title' => $annonce->getTitre(),
                'start' => $annonce->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $annonce->getDateFin()->format('Y-m-d H:i:s'),
                'priority' => $annonce->getPriorite(),
                'content' => $annonce->getContenu(),
                'autor' => $annonce->getAuteur(),
                'created' => $annonce->getDateCreation()->format('Y-m-d H:i:s'),
                'ressourceId' => $annonce->getAuteur()
            ];

            $events[] = $event;
        }

        return $this->json($events);
    }
    

    public function getTraitementEvents(EntityManagerInterface $entityManager): JsonResponse
    {
        $traitements = $entityManager->getRepository(Treatment::class)->findAll();

        $events = [];

        foreach ($traitements as $traitement) {
                $numSemaine = 'Semaine ' . date('W', strtotime($traitement->getDateDebut()->format('Y-m-d')));

                $event = [
                    'id' => $traitement->getId(),
                    'title' => $traitement->getId(),
                    'start' => $traitement->getDateDebut()->format('Y-m-d H:i:s'),
                    'end' => $traitement->getDateFin()->format('Y-m-d H:i:s'),
                    'poolId' => [],
                    'poolName' => [],
                    'VmId' => [],
                    'VmName' => [],
                    'caisseId' => $traitement->getIdCaisse()->getId(),
                    'caisseNum' => $traitement->getIdCaisse()->getNumCaisse(),
                    'caisseName' => $traitement->getIdCaisse()->getNomCaisse(),
                    'caisseExploitId' => $traitement->getIdCaisseExploit()->getId(),
                    'caisseExploitNum' => $traitement->getIdCaisseExploit()->getNumCaisse(),
                    'caisseExploitName' => $traitement->getIdCaisseExploit()->getNomCaisse(),
                    'scenarioId' => $traitement->getIdScenario()->getId(),
                    'scenarioName' => $traitement->getIdScenario()->getNomScenario(),
                    'processId' => [],
                    'processName' => [],
                    'nbrDossier' => $traitement->getNbreDossier(),
                    'etat' => $traitement->getEtat(),
                    'errorComment' => $traitement->getErrorComment(),
                    'clotureComment' => $traitement->getClotureComment(),
                    'resourceId' => [],
                    'etape' => $traitement->getEtape(),
                    'numSemaine' => $numSemaine,
                    'dateFinCloture' => $traitement->getDateFinCloture() ? $traitement->getDateFinCloture()->format('d/m/Y à H:i:s') : null,
                    'dateFinError' => $traitement->getDateFinError() ? $traitement->getDateFinError()->format('d/m/Y à H:i:s') : null,
                    'periodicity' => $traitement->getIdScenario()->getPeriodicity()
                ];

                foreach ($traitement->getIdPool() as $pool) {
                    $event['poolId'][] = $pool->getId();
                }

                foreach ($traitement->getIdPool() as $pool) {
                    $event['poolName'][] = $pool->getNomPool();
                }

                foreach ($traitement->getIdPool() as $ressources) {
                    $event['resourceId'][] = $ressources->getNomPool();
                }

                foreach ($traitement->getIdProcess() as $process) {
                    $event['processId'][] = $process->getId();
                }

                foreach ($traitement->getIdProcess() as $process) {
                    $event['processName'][] = $process->getNomProcess();
                }

                foreach ($traitement->getIdVm() as $vm) {
                    $event['VmId'][] = $vm->getId();
                }

                foreach ($traitement->getIdVm() as $vm) {
                    $event['VmName'][] = $vm->getNomVm();
                }

                $events[] = $event;
        }

        return $this->json($events);
    }


    public function getPoolVMResources(EntityManagerInterface $entityManager): JsonResponse
    {
        $poolVMs =  $entityManager->getRepository(PoolVm::class)->findAll();
        $annonces = $entityManager->getRepository(Message::class)->findAll();

        $resources = [];

        foreach ($annonces as $annonce) {

            $resource = [
                'id' => $annonce->getAuteur(),
                'title' => '* ANNONCE(S) *'
            ];

            $resources[] = $resource;
        }

        
        foreach ($poolVMs as $poolVM) {

            $resource = [
                'id' => $poolVM->getNomPool(),
                'title' => $poolVM->getNomPool()
            ];

            $resources[] = $resource;
        }

        // Tri par ordre croissant basé sur le titre
        usort($resources, function($a, $b) {
            return strcmp($a['title'], $b['title']);
        });

        return $this->json($resources);
    }


    public function deleteTraitement($id, UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);
    
        if (!$traitement) {
            $this->addFlash('error', 'Traitement non trouvé.');
            return $this->redirectToRoute('home');  
        }
        
        // Récupérer les traitements enfants
        $childTraitements = $traitement->getReplanifications();
    
        // Supprimer les traitements enfants
        foreach ($childTraitements as $childTraitement) {
            // Création du message de log pour le traitement enfant
            $logMessage = 'Suppression d\'un traitement (n° ' .$childTraitement->getId() . ') : 
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
    
            // Création et enregistrement du log pour le traitement enfant
            $this->createLog($entityManager, $user, $logMessage);
    
            // Supprimer le traitement enfant
            $entityManager->remove($childTraitement);
        }
    
        // Supprimer le traitement parent
        // Création du message de log pour le traitement parent
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
    
        // Création et enregistrement du log pour le traitement parent
        $this->createLog($entityManager, $user, $logMessage);
    
        // Supprimer le traitement parent
        $entityManager->remove($traitement);
    
        // Enregistrer les suppressions
        $entityManager->flush();
    
        // Redirection vers la page des paramètres après la suppression
        $this->addFlash('success', 'Traitement supprimé.');

        $responseData = [
            'success' => true,
        ];

        return new JsonResponse($responseData);
    }























    public function clotureTraitement(UserInterface $user, EntityManagerInterface $entityManager, Request $request, $id): JsonResponse
    {
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

        $data = $request->request->all();

        $commentaire = $data['commentaire'];
        $dateFinCloture =  new \DateTime($data['dateFinCloture']);

        if ($traitement->getEtat() === "Erreur") {

            $this->addFlash('error', 'Le traitement ne peut pas être clôturé car il est déjà mis en erreur.');

            $responseData = [
                'error' => true,
            ];

        } else {
            $traitement->setEtat('Clôturé');
            $traitement->setClotureComment($commentaire);
            $traitement->setDateFinCloture($dateFinCloture);

            $traitement->setDateFin($dateFinCloture);
            
            $this->addFlash('success', 'Le traitement a été clôturé.');

            $entityManager->persist($traitement);

            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Mise en clôture d\'un traitement (n° ' .$traitement->getId() . ') : 
                            Date de fin : ' . $traitement->getDateFinCloture()->format('d/m/Y à H:i:s') .  
                            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' .
                            ' Commentaire : ' . $traitement->getClotureComment()
                            );
    
            $entityManager->persist($logs);
    
            $entityManager->flush();

            $responseData = [
                'success' => true,
            ];
        }

        return new JsonResponse($responseData);
    }


    public function erreurTraitement(UserInterface $user, EntityManagerInterface $entityManager, Request $request, $id): JsonResponse
    {
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

        $data = $request->request->all();

        $commentaire = $data['commentaire'];
        $dateFinErreur =  new \DateTime($data['dateFinErreur']);

        if ($traitement->getEtat() === "Clôturé") {

            $this->addFlash('error', 'Le traitement ne peut pas être mis en erreur car il est déjà clôturé.');

            $responseData = [
                'error' => true,
            ];

        } else {
            $traitement->setEtat('Erreur');
            $traitement->setErrorComment($commentaire);
            $traitement->setDateFinError($dateFinErreur);
            
            $this->addFlash('success', 'Le traitement a été mis en erreur.');

            $entityManager->persist($traitement);

            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Mise en erreur d\'un traitement (n° ' .$traitement->getId() . ') :
                            Date de fin : ' . $traitement->getDateFinError()->format('d/m/Y à H:i:s') . 
                            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' .
                            ' Commentaire : ' . $traitement->getErrorComment()
                            );
    
            $entityManager->persist($logs);
    
            $entityManager->flush();
    
            $responseData = [
                'success' => true,
            ];
        }

        return new JsonResponse($responseData);
    }    
     










    public function deleteCloture(UserInterface $user, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

        if ($traitement->getEtat() === "Erreur" || $traitement->getEtat() === "En cours") {

            $this->addFlash('error', 'La clôture ne peut pas être supprimé car elle n\'existe pas.');

            $responseData = [
                'error' => true,
            ];

        } else {

            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Suppression d\'une clôture d\'un traitement (n° ' .$traitement->getId() . ')' . 
                            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' . 
                            ' Commentaire : ' . $traitement->getClotureComment()
                            );
    
            $entityManager->persist($logs);
            $entityManager->flush($logs);

            $traitement->setEtat('En cours');
            $traitement->setClotureComment(null);
            $traitement->removeDateFinCloture();

            $this->addFlash('success', 'La clôture a été être supprimé.');

            $entityManager->persist($traitement);
            $entityManager->flush($traitement);
    
            $responseData = [
                'success' => true,
            ];
        }

        return new JsonResponse($responseData);
    }























    public function deleteError(UserInterface $user, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $traitement = $entityManager->getRepository(Treatment::class)->find($id);

        if ($traitement->getEtat() === "Clôturé" || $traitement->getEtat() === "En cours") {

            $this->addFlash('error', 'L\'erreur ne peut pas être supprimé car elle n\'existe pas.');

            $responseData = [
                'error' => true,
            ];

        } else {
            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Suppression d\'une erreur d\'un traitement (n° ' .$traitement->getId() . ')' .
                            ' Caisse : ' . $traitement->getIdCaisse()->getNomCaisse() . ' (' . $traitement->getIdCaisse()->getNumCaisse() . ') ' . 
                            ' Commentaire : ' . $traitement->getErrorComment()
                            );
    
            $entityManager->persist($logs);
            $entityManager->flush($logs);

            $traitement->setEtat('En cours');
            $traitement->setErrorComment(null);
            $traitement->removeDateFinError();

            $this->addFlash('success', 'L\'erreur a été être supprimé.');

            $entityManager->persist($traitement);
            $entityManager->flush($traitement);
    
            $responseData = [
                'success' => true,
            ];
        }

        return new JsonResponse($responseData);
    }    























    public function verifyRole(UserInterface $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $roleUser = $user->getRoles();
        $numCaisse = $user->getCodeOrganisme();
        $idCaisse = null;

        if($numCaisse == "941" || $numCaisse == "841") {
            $idCaisse = $entityManager->getRepository(CaisseExploit::class)->findOneBy(['num_caisse' => $numCaisse]);
        }

        if (in_array('EXPLOITANT', $roleUser) || in_array('ADMINISTRATEUR', $roleUser)) {
            $responseData = [
                'success' => true,
                'role' => 'Rôle accepté.',
                'idCaisse' => $idCaisse ? $idCaisse->getId() : null
            ];
        } else {
            $responseData = [
                'success' => false,
                'message' => 'Accès refusé. Vous n\'avez pas le rôle requis.',
            ];
        }

        return new JsonResponse($responseData);
    }



















    private function getVmNamesString(Treatment $traitement): string
    {
        $vmNames = [];
        foreach ($traitement->getIdVm() as $traitement) {
            $vmNames[] = $traitement->getNomVm();
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
        foreach ($traitement->getIdProcess() as $traitement) {
            $processNames[] = $traitement->getNomProcess();
        }
    
        return implode(', ', $processNames);
    }




    public function holidays(): JsonResponse
    {
        $holidaysFilePath = $this->getParameter('kernel.project_dir') . '/public/holidays.json';

        // Vérifie si le fichier existe
        if (file_exists($holidaysFilePath)) {
            // Récupère le contenu du fichier
            $holidaysData = file_get_contents($holidaysFilePath);
            
            // Parse le contenu JSON
            $holidays = json_decode($holidaysData);
    
            $responseData = [
                'success' => true,
                'data' => $holidays,
            ];
        } else {
            $responseData = [
                'success' => false,
                'message' => 'Le fichier holidays.json n\'existe pas.',
            ];
        }

        return new JsonResponse($responseData);
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
