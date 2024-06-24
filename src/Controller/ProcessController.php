<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProcessType;
use App\Form\UpdateProcessType;
use App\Entity\Process; 
use App\Entity\Logs;

class ProcessController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    

    public function createProcess(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $process = new Process();
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingProcess = $entityManager->getRepository(Process::class)->findOneBy(['nom_process' => $process->getNomProcess()]);

            if ($existingProcess) {

                $this->addFlash('error', 'Ce nom est déjà utilisé.');
                
                return $this->redirectToRoute('process_create');
            }

            $nomScenario = $process->getNomProcess();

            if (trim($nomScenario) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('process_create'); 
            }


            if ($process->getStartTime() > $process->getEndTime()) {
                $this->addFlash('error', 'L\'heure de début doit être inférieure à l\'heure de fin.');
                return $this->redirectToRoute('process_create'); 
            } 
        
            if ($process->getStartTime() !== null || $process->getEndTime() !== null) {
                if ($process->getStartTime() == $process->getEndTime()) {
                    $this->addFlash('error', 'L\'heure de début doit être différente de l\'heure de fin.');
                    return $this->redirectToRoute('process_create');
                } else {
                    $entityManager->persist($process);
                    $entityManager->flush();

                    $scenarioNamesString = $this->getScenarioNamesString($process);

                    // Enregistrement des logs
                    $logs = new Logs();
                    $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                    $logs->setDate(new \DateTime());
                    $logs->setAction('Création d\'un process : ' . $process->getNomProcess() .
                                    ' Temps / min / dossier : ' . $process->getTempsMinuteDossier() .
                                    ' Utilisable sur une seule VM : ' . ($process->getVmMax() ? 'Oui' : 'Non') .
                                    ' Tourne week-ends et jours fériés : ' . ($process->getWeekdsendsAndHolidays() ? 'Non' : 'Oui') .
                                    ' Scenario associé : '  . $scenarioNamesString
                                    );
                                    
                    $entityManager->persist($logs);
                    $entityManager->flush();
                    $this->addFlash('success', 'Process ajouté.');
                    return $this->redirectToRoute('process_create');
                }
            }
            

            else {
                $entityManager->persist($process);
                $entityManager->flush();

                $scenarioNamesString = $this->getScenarioNamesString($process);

                // Enregistrement des logs
                $logs = new Logs();
                $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                $logs->setDate(new \DateTime());
                $logs->setAction('Création d\'un process : ' . $process->getNomProcess() .
                                ' Temps / min / dossier : ' . $process->getTempsMinuteDossier() .
                                ' Utilisable sur une seule VM : ' . ($process->getVmMax() ? 'Oui' : 'Non') .
                                ' Tourne week-ends et jours fériés : ' . ($process->getWeekdsendsAndHolidays() ? 'Non' : 'Oui') .
                                ' Scenario associé : '  . $scenarioNamesString
                                );

                $entityManager->persist($logs);
                $entityManager->flush();

                $this->addFlash('success', 'Process ajouté.');

                return $this->redirectToRoute('setting');
            }
        }

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('process/create.process.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'form' => $form->createView(),
        ]);
    }


    public function deleteProcess(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    {
        $process = $entityManager->getRepository(Process::class)->find($id);
    
        if (!$process) {
            $this->addFlash('error', 'Process non trouvé.');
            return $this->redirectToRoute('setting');  
        }
    
        // Vérifier si le process appartient à un traitement
        $traitements = $process->getIdTraitement();

        if ($traitements->count() > 0) {
            $this->addFlash('error', 'Ce process appartient à des traitements. Veuillez supprimer les traitements avant de supprimer le process.');
            return $this->redirectToRoute('setting');
        }
        
        $scenarioNamesString = $this->getScenarioNamesString($process);

        // Enregistrement des logs
        $logs = new Logs();
        $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
        $logs->setDate(new \DateTime());
        $logs->setAction('Suppression d\'un process : ' . $process->getNomProcess() .
                        ' Temps / min / dossier : ' . $process->getTempsMinuteDossier() .
                        ' Utilisable sur une seule VM : ' . ($process->getVmMax() ? 'Oui' : 'Non') .
                        ' Tourne week-ends et jours fériés : ' . ($process->getWeekdsendsAndHolidays() ? 'Non' : 'Oui') .
                        ' Scenario associé : '  . $scenarioNamesString
                        );

        $entityManager->persist($logs);

        $entityManager->remove($process);
        $entityManager->flush();

        $this->addFlash('success', 'Process supprimé.');
    
        return $this->redirectToRoute('setting');  
    }


    
    public function editProcess(UserInterface $user, Request $request, Process $process, EntityManagerInterface $entityManager): Response
        {
            // Récupération des scénarios associés avant modification
            $originalScenarios = $process->getIdScenario()->toArray();

            $form = $this->createForm(UpdateProcessType::class, $process);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $nomProcess = $process->getNomProcess();
                $tempsMinuteDossier = $process->getTempsMinuteDossier();
                $vmMax = $process->getVmMax();
                $weekdsendsAndHoliday = $process->getWeekdsendsAndHolidays();
                $startTime = $process->getStartTime();
                $endTime = $process->getEndTime();

                // Récupération des nouvelles valeurs du formulaire
                $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($process);

                // Vérification des scénarios associés après modification
                $updatedScenarios = $process->getIdScenario()->toArray();

                // Comparaison des valeurs pour détecter les modifications
                $scenariosChanged = count(array_diff($originalScenarios, $updatedScenarios)) > 0 || count(array_diff($updatedScenarios, $originalScenarios)) > 0;

                // Comparaison des valeurs
                if ($originalData['nom_process'] === $nomProcess &&
                    $originalData['temps_minute_dossier'] === $tempsMinuteDossier &&
                    $originalData['vm_max'] === $vmMax &&
                    $originalData['weekdsends_and_holidays'] === $weekdsendsAndHoliday &&
                    $originalData['start_time'] === $startTime &&
                    $originalData['end_time'] === $endTime &&
                    !$scenariosChanged) {
            
                    $this->addFlash('error', 'Aucune modification détectée.');
                    return $this->redirectToRoute('process_edit', ['id' => $process->getId()]);
                }

                if (trim($nomProcess) === '') {
                    $this->addFlash('error', 'Le nom ne peut pas être vide.');
                    return $this->redirectToRoute('process_edit', ['id' => $process->getId()]);
                }


                if ($process->getStartTime() > $process->getEndTime()) {
                    $this->addFlash('error', 'L\'heure de début doit être inférieur à l\'heure de fin.');
                    return $this->redirectToRoute('process_edit', ['id' => $process->getId()]);
                } 

                if ($process->getStartTime() !== null || $process->getEndTime() !== null) {
                    if ($process->getStartTime() == $process->getEndTime()) {
                        $this->addFlash('error', 'L\'heure de début doit être différente de l\'heure de fin.');
                        return $this->redirectToRoute('process_edit', ['id' => $process->getId()]);
                    } else {
                        $scenarioNamesString = $this->getScenarioNamesString($process);

                        // Enregistrement des logs
                        $logs = new Logs();
                        $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                        $logs->setDate(new \DateTime());
                        $logs->setAction('Modification d\'un process : ' . $process->getNomProcess() .
                                        ' Temps / min / dossier : ' . $process->getTempsMinuteDossier() .
                                        ' Utilisable sur une seule VM : ' . ($process->getVmMax() ? 'Oui' : 'Non') .
                                        ' Tourne week-ends et jours fériés : ' . ($process->getWeekdsendsAndHolidays() ? 'Non' : 'Oui') .
                                        ' Scenario associé : '  . $scenarioNamesString
                                        );

                        $entityManager->persist($logs);
                        $entityManager->flush();

                        $this->addFlash('success', 'Process modifié.');

                        return $this->redirectToRoute('setting');
                    }
                }

                else {
                    $entityManager->persist($process);
                    $entityManager->flush();
                    
                    $scenarioNamesString = $this->getScenarioNamesString($process);

                    // Enregistrement des logs
                    $logs = new Logs();
                    $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                    $logs->setDate(new \DateTime());
                    $logs->setAction('Modification d\'un process : ' . $process->getNomProcess() .
                                    ' Temps / min / dossier : ' . $process->getTempsMinuteDossier() .
                                    ' Utilisable sur une seule VM : ' . ($process->getVmMax() ? 'Oui' : 'Non') .
                                    ' Tourne week-ends et jours fériés : ' . ($process->getWeekdsendsAndHolidays() ? 'Non' : 'Oui') .
                                    ' Scenario associé : '  . $scenarioNamesString
                                    );

                    $entityManager->persist($logs);

                    $entityManager->flush();

                    $this->addFlash('success', 'Process modifié.');

                    return $this->redirectToRoute('process_edit', ['id' => $process->getId()]);
                }
            }

            $num_agent = $user->getNumeroAgent();
            $nom_agent = $user->getNom();
            $prenom_agent = $user->getPrenom();

            return $this->render('process/edit.process.html.twig', [
                'num_agent' => $num_agent,
                'nom_agent' => $nom_agent,
                'prenom_agent' => $prenom_agent,
                'process' => $process,
                'form' => $form->createView(),
            ]);
        }




        private function getScenarioNamesString(Process $process): string
        {
            $scenarioNames = [];
            foreach ($process->getIdScenario() as $scenario) {
                $scenarioNames[] = $scenario->getNomScenario();
            }
        
            return implode(', ', $scenarioNames);
        }
}
