<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ScenarioType;
use App\Form\UpdateScenarioType;
use App\Entity\Scenario;
use App\Entity\Logs;

class ScenarioController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    

    public function createScenario(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $scenario = new Scenario();
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existingScenario = $entityManager->getRepository(Scenario::class)->findOneBy(['nom_scenario' => $scenario->getNomScenario()]);
            if ($existingScenario) {
                $this->addFlash('error', 'Ce nom est déjà utilisé.');

                return $this->redirectToRoute('scenario_create'); 
            } 

            $nomScenario = $scenario->getNomScenario();
            
            if (trim($nomScenario) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('scenario_create'); 
            }

            $delai1 = $scenario->getDelai1();
            $delai2 = $scenario->getDelai2();
            $delai3 = $scenario->getDelai3();

            if (stripos($nomScenario, 'puma') === false && ($delai1 !== null || $delai2 !== null || $delai3 !== null)) {
                $this->addFlash('error', 'Si le nom du scénario ne contient pas "puma", aucun délai ne doit être saisi.');
                return $this->redirectToRoute('scenario_create');
            }

            $periodicity = $scenario->getPeriodicity();
            $periodicity_until = $scenario->getUntilWhen();

            if (stripos($nomScenario, 'puma') !== false && ($periodicity !== 'Aucune' || $periodicity_until !== null)) {
                $this->addFlash('error', 'Les périodes sont réservés au scénario non PUMA.');
                return $this->redirectToRoute('scenario_create');
            }
            
            else {

                $entityManager->persist($scenario);
                $entityManager->flush();

                // Enregistrement des logs
                $logs = new Logs();
                $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                $logs->setDate(new \DateTime());
                $logs->setAction('Création d\'un scenario : ' . $scenario->getNomScenario() .
                                ' Délai 1 : ' . ($scenario->getDelai1() ?? 0).
                                ' Délai 2 : ' . ($scenario->getDelai2() ?? 0).
                                ' Délai 3 : ' . ($scenario->getDelai3() ?? 0).
                                ' Périodicité : ' . ($scenario->getPeriodicity())
                                );

                $entityManager->persist($logs);
                $entityManager->flush();                                                            
                
                $this->addFlash('success', 'Scenario ajouté.');

                return $this->redirectToRoute('setting');

            }
        }

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('scenario/create.scenario.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'form' => $form->createView(),
        ]);
    }


    public function deleteScenario(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    {
        $scenario = $entityManager->getRepository(Scenario::class)->find($id);
    
        if (!$scenario) {
            $this->addFlash('error', 'Scénario non trouvé.');
            return $this->redirectToRoute('setting');  
        }

        // Vérifier si le scenario appartient à un traitement
        $traitements = $scenario->getIdTraitement();

        if ($traitements->count() > 0) {
            $this->addFlash('error', 'Ce scénario appartient à des traitements. Veuillez supprimer les traitements avant de supprimer le scénario.');
            return $this->redirectToRoute('setting');
        }

        // Vérifier si la scenari contient des process
        $processInScenario = $scenario->getProcesses();

        if ($processInScenario->count() > 0) {
            // La Pool contient des VM, ajouter un message flash
            $this->addFlash('error', 'Ce scénario contient un ou plusieurs process. Veuillez tous les supprimer, avant de supprimer le scénario.');
            return $this->redirectToRoute('setting');
        }
    
        // Enregistrement des logs
        $logs = new Logs();
        $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
        $logs->setDate(new \DateTime());
        $logs->setAction('Suppression d\'un scenario : ' . $scenario->getNomScenario() .
                        ' Délai 1 : ' . ($scenario->getDelai1() ?? 0).
                        ' Délai 2 : ' . ($scenario->getDelai2() ?? 0).
                        ' Délai 3 : ' . ($scenario->getDelai3() ?? 0).
                        ' Périodicité : ' . ($scenario->getPeriodicity())
                        );

        $entityManager->persist($logs);

        $entityManager->remove($scenario);
        $entityManager->flush();

        $this->addFlash('success', 'Scénario supprimé.');
    
        return $this->redirectToRoute('setting');  
    }

    

    public function editScenario(UserInterface $user, Request $request, Scenario $scenario, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UpdateScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nomScenario = $scenario->getNomScenario();
            $delai1 = $scenario->getDelai1();
            $delai2 = $scenario->getDelai2();
            $delai3 = $scenario->getDelai3();
            $periodicity = $scenario->getPeriodicity();
            $periodicity_until = $scenario->getUntilWhen();

            // Récupération des nouvelles valeurs du formulaire
            $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($scenario);
            $originalNom = $originalData['nom_scenario'];
            $originalDelai1 = $originalData['delai1'];
            $originalDelai2 = $originalData['delai2'];
            $originalDelai3 = $originalData['delai3'];
            $originalPeriodicity = $originalData['periodicity'];
            $originalPeriodicityUntil = $originalData['until_when'];

            // Comparaison des valeurs
            if ($originalNom === $nomScenario &&
                $originalDelai1 === $delai1 &&
                $originalDelai2 === $delai2 &&
                $originalDelai3 === $delai3 &&
                $originalPeriodicity === $periodicity &&
                $originalPeriodicityUntil === $periodicity_until) {
                $this->addFlash('error', 'Aucune modification détectée.');
                return $this->redirectToRoute('scenario_edit', ['id' => $scenario->getId()]);
            }

            if (trim($nomScenario) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('scenario_edit', ['id' => $scenario->getId()]);
            }
            
            if (stripos($nomScenario, 'puma') === false && ($delai1 !== null || $delai2 !== null || $delai3 !== null)) {
                $this->addFlash('error', 'Si le nom du scénario ne contient pas "puma", aucun délai ne doit être saisi.');
                return $this->redirectToRoute('scenario_edit', ['id' => $scenario->getId()]);
            }
    
            if (stripos($nomScenario, 'puma') !== false && ($periodicity !== 'Aucune' || $periodicity_until !== null)) {
                $this->addFlash('error', 'Les périodes sont réservés au scénario non PUMA.');
                return $this->redirectToRoute('scenario_edit', ['id' => $scenario->getId()]);
            }

            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Modification d\'un scenario : ' . $scenario->getNomScenario() .
                            ' Délai 1 : ' . ($scenario->getDelai1() ?? 0).
                            ' Délai 2 : ' . ($scenario->getDelai2() ?? 0).
                            ' Délai 3 : ' . ($scenario->getDelai3() ?? 0).
                            ' Périodicité : ' . ($scenario->getPeriodicity())
                            );

            $entityManager->persist($logs);

            $entityManager->flush();

            $this->addFlash('success', 'Scenario modifié.');

            return $this->redirectToRoute('setting');
        }
        
        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('scenario/edit.scenario.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'scenario' => $scenario,
            'form' => $form->createView(),
        ]);
    }
}
