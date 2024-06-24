<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CaisseExploitRepository;
use App\Repository\CaisseRepository;
use App\Repository\PoolVmRepository;
use App\Repository\VmRepository;
use App\Repository\ProcessRepository;
use App\Repository\ScenarioRepository;
use App\Repository\MessageRepository;
use App\Repository\TreatmentRepository;
use App\Entity\Caisse;
use App\Repository\LogsRepository;




class HomeController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    
    public function index(CaisseExploitRepository $CaisseExploitRepository, CaisseRepository $CaisseRepository, PoolVmRepository $PoolVmRepository, 
                        VmRepository $VmRepository, ProcessRepository $ProcessRepository,
                        ScenarioRepository $ScenarioRepository, MessageRepository $MessageRepository,
                        UserInterface $user): Response 
    {

        $listAllMessage = $MessageRepository->findAll();

        $listAllScenario = $ScenarioRepository->findAll();     

        $listAllProcess = $ProcessRepository->findAll();

        $listAllPoolVM = $PoolVmRepository->findAll();

        $listAllCaisse = $CaisseRepository->findAll();

        $listAllCaisseExploit = $CaisseExploitRepository->findAll();
        
        $listAllVM = $VmRepository->findAll();

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();
        $user = $this->getUser();


        return $this->render('home.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'user' => $user,
            'controller_name' => 'HomeController',
            'listAllMessage' => $listAllMessage,
            'listAllCaisse' => $listAllCaisse,
            'listAllCaisseExploit' => $listAllCaisseExploit,
            "listAllPoolVM" => $listAllPoolVM,
            "listAllVM" => $listAllVM,
            "listAllProcess" => $listAllProcess,
            "listAllScenario" => $listAllScenario
        ]);
    }




    public function traitment_edit_extern (CaisseExploitRepository $CaisseExploitRepository, CaisseRepository $CaisseRepository, PoolVmRepository $PoolVmRepository, 
                                            VmRepository $VmRepository, ProcessRepository $ProcessRepository,
                                            ScenarioRepository $ScenarioRepository, TreatmentRepository $TreatmentRepository, MessageRepository $MessageRepository,
                                            Request $request, UserInterface $user ,EntityManagerInterface $entityManager): Response 
    {

        $listAllMessage = $MessageRepository->findAll();

        $listAllScenario = $ScenarioRepository->findAll();     

        $listAllProcess = $ProcessRepository->findAll();

        $listAllPoolVM = $PoolVmRepository->findAll();

        $listAllCaisse = $CaisseRepository->findAll();

        $listAllCaisseExploit = $CaisseExploitRepository->findAll();
        
        $listAllVM = $VmRepository->findAll();

        $listAllTraitement = $TreatmentRepository->findAll();

        $selectedDate = $request->query->get('selectedDate');

        $listAllTraitement = $TreatmentRepository->findTraitementsForDate(new \DateTimeImmutable($selectedDate));

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();
        $user = $this->getUser();

        return $this->render('home.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'user' => $user,
            'controller_name' => 'HomeController',
            'listAllMessage' => $listAllMessage,
            'listAllCaisse' => $listAllCaisse,
            'listAllCaisseExploit' => $listAllCaisseExploit,
            "listAllPoolVM" => $listAllPoolVM,
            "listAllVM" => $listAllVM,
            "listAllProcess" => $listAllProcess,
            "listAllScenario" => $listAllScenario,
            "listAllTraitement" => $listAllTraitement
        ]);
    }












    public function infoCaisse (CaisseExploitRepository $CaisseExploitRepository, CaisseRepository $CaisseRepository, PoolVmRepository $PoolVmRepository, 
                                VmRepository $VmRepository, ProcessRepository $ProcessRepository, LogsRepository $LogsRepository,
                                ScenarioRepository $ScenarioRepository, TreatmentRepository $TreatmentRepository, 
                                MessageRepository $MessageRepository, EntityManagerInterface $entityManager, UserInterface $user, $id): Response 
    {

        $caisse = $entityManager->getRepository(Caisse::class)->find($id);

        $numCaisse = $caisse->getNumCaisse();

        $currentUser = $user->getCodeOrganisme();

        $roleUser = $user->getRoles();

        if ($currentUser == $numCaisse || in_array('EXPLOITANT', $roleUser) || in_array('ADMINISTRATEUR', $roleUser) ) {
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas accéder à la page de cette caisse.');
            return $this->redirectToRoute('home');
        }

        $listAllMessage = $MessageRepository->findAll();

        $listAllScenario = $ScenarioRepository->findAll();     

        $listAllProcess = $ProcessRepository->findAll();

        $listAllPoolVM = $PoolVmRepository->findAll();

        $listAllCaisse = $CaisseRepository->findAll();

        $listAllCaisseExploit = $CaisseExploitRepository->findAll();
        
        $listAllVM = $VmRepository->findAll();

        $listAllTraitement = $TreatmentRepository->findAll();

        $listAllCaisse = $CaisseRepository->findAll();

        $listAllTraitement = $TreatmentRepository->findAll();

        $listAllLogs = $LogsRepository->findAll();

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('caisse/info_caisse.html.twig',
        [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'listAllMessage' => $listAllMessage,
            'listAllCaisse' => $listAllCaisse,
            'listAllCaisseExploit' => $listAllCaisseExploit,
            "listAllPoolVM" => $listAllPoolVM,
            "listAllVM" => $listAllVM,
            "listAllProcess" => $listAllProcess,
            "listAllScenario" => $listAllScenario,
            "listAllTraitement" => $listAllTraitement,
            "caisse" => $caisse,
            'listAllLogs' => $listAllLogs,
            'roleUser' => $roleUser
        ]
        );
    }

















    public function get_profile(LogsRepository $LogsRepository, UserInterface $user, EntityManagerInterface $entityManager): Response 
    {

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent =  $user->getPrenom();
        $role = implode(', ', $user->getRoles());
        $identifiant = $user->getIdentifiant();
        $codeOrganisme = $user->getCodeOrganisme();

        $listAllLogs = $LogsRepository->findAll();

        return $this->render('profile/index.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'role' => $role,
            'identifiant' => $identifiant,
            'codeOrganisme' => $codeOrganisme,
            'listAllLogs' => $listAllLogs
        ]);
    }
















    public function getAllProcesses(ProcessRepository $processRepository, Request $request): JsonResponse
    {
        $currentDate = new \DateTime($request->query->get('currentDate'));
        $endDate = new \DateTime($request->query->get('endDate'));

        $listAllProcesses = $processRepository->findAll();

        // Initialiser un tableau pour stocker le pourcentage d'utilisation par process
        $processData = [];

        // Boucle sur chaque process
        foreach ($listAllProcesses as $process) {
            // Récupérer les traitements associés au process
            $treatments = $process->getIdTraitement();

            // Initialiser les compteurs pour la durée totale par jour, semaine, mois et année pour chaque process
            $totalDurationDayByProcess = 0;
            $totalDurationWeekByProcess = 0;
            $totalDurationMonthByProcess = 0;
            $totalDurationYearByProcess = 0;

            $totalDurationDayByProcessInHours =  0;
            $totalDurationWeekByProcessInHours =  0;
            $totalDurationMonthByProcessInHours = 0;
            $totalDurationYearByProcessInHours =  0;

            // Calculer le pourcentage d'utilisation par jour, par semaine et par mois pour chaque process
            $secondsInDay = 24 * 60 * 60;
            $secondsInWeek = 7 * $secondsInDay;
            $secondsInMonth = 30 * $secondsInDay;
            $secondsInYear = 365 * $secondsInDay;

            // Boucle sur chaque traitement associé au process
            foreach ($treatments as $treatment) {
                if ($treatment->getEtat() == 'En cours' && $this->isTreatmentContains($treatment, $currentDate, $endDate)) {
                    // Calculer la durée du traitement
                    $durationInSeconds = $treatment->getDateFin()->getTimestamp() - $treatment->getDateDebut()->getTimestamp();

                    if ($treatment->getDateFin() > $endDate) {
                        $durationUntilEndDate = $endDate->getTimestamp() - $treatment->getDateDebut()->getTimestamp();
                        $durationInSeconds = max(0, $durationUntilEndDate);
                    }

                    $startDateTimestamp = $treatment->getDateDebut()->getTimestamp();
                    $endDateTimestamp = $treatment->getDateFin()->getTimestamp();
                    if ($startDateTimestamp < $currentDate->getTimestamp()) {
                        $startDateTimestamp = $currentDate->getTimestamp();
                        $durationInSeconds = $endDateTimestamp - $startDateTimestamp;
                    }

                    // Ajouter la durée au compteur approprié pour chaque process
                    $totalDurationDayByProcess += min($durationInSeconds, $secondsInDay);
                    $totalDurationWeekByProcess += min($durationInSeconds, $secondsInWeek);
                    $totalDurationMonthByProcess += min($durationInSeconds, $secondsInMonth);
                    $totalDurationYearByProcess += min($durationInSeconds, $secondsInYear);

                    // Convertir la durée totale en heures
                    $totalDurationDayByProcessInHours = $totalDurationDayByProcess / 3600;
                    $totalDurationWeekByProcessInHours = $totalDurationWeekByProcess / 3600;
                    $totalDurationMonthByProcessInHours = $totalDurationMonthByProcess / 3600;
                    $totalDurationYearByProcessInHours = $totalDurationYearByProcess / 3600;
                }
            }

            // Calculate percentages with a maximum of 100%
            $percentageDay = min(($totalDurationDayByProcess / $secondsInDay) * 100, 100);
            $percentageWeek = min(($totalDurationWeekByProcess / $secondsInWeek) * 100, 100);
            $percentageMonth = min(($totalDurationMonthByProcess / $secondsInMonth) * 100, 100);
            $percentageYear = min(($totalDurationYearByProcess / $secondsInYear) * 100, 100);


            $processData[] = [
                'nom_process' => $process->getNomProcess(),
                'percentageDay' => $percentageDay,
                'percentageWeek' => $percentageWeek,
                'percentageMonth' => $percentageMonth,
                'percentageYear' => $percentageYear,
                'totalDurationDayByProcessInHours' => $totalDurationDayByProcessInHours,
                'totalDurationWeekByProcessInHours' => $totalDurationWeekByProcessInHours,
                'totalDurationMonthByProcessInHours' => $totalDurationMonthByProcessInHours,
                'totalDurationYearByProcessInHours' => $totalDurationYearByProcessInHours,
            ];
        }

        return new JsonResponse($processData);
    }















    public function getAllPools(PoolVmRepository $poolVmRepository, Request $request): JsonResponse
    {
        $currentDate = new \DateTime($request->query->get('currentDate'));
        $endDate = new \DateTime($request->query->get('endDate'));

        $listAllPool = $poolVmRepository->findAll();

        // Initialiser un tableau pour stocker les données de chaque piscine
        $poolData = [];

        // Boucle sur chaque piscine
        foreach ($listAllPool as $pool) {
            // Récupérer les machines virtuelles associées à la piscine
            $nomVmArray = [];
            $vms = $pool->getNomVm();
            foreach ($vms as $vm) {
                $nomVmArray[] = $vm->getNomVm();
            }

            // Initialiser un tableau pour stocker les noms de VM utilisées dans cette piscine
            $vmUse = [];

            // Récupérer les traitements associés à la piscine
            $treatments = $pool->getIdTraitement();

            // Initialiser les compteurs pour la durée totale par jour, semaine, mois et année pour chaque process
            $totalDurationDayByPool = 0;
            $totalDurationWeekByPool = 0;
            $totalDurationMonthByPool = 0;
            $totalDurationYearByPool = 0;

            $totalDurationDayByPoolInHours =  0;
            $totalDurationWeekByPoolInHours =  0;
            $totalDurationMonthByPoolInHours = 0;
            $totalDurationYearByPoolInHours =  0;

            // Calculer le pourcentage d'utilisation par jour, par semaine et par mois pour chaque process
            $secondsInDay = 24 * 60 * 60;
            $secondsInWeek = 7 * $secondsInDay;
            $secondsInMonth = 30 * $secondsInDay;
            $secondsInYear = 365 * $secondsInDay;


            // Boucle sur chaque traitement associé à la piscine
            foreach ($treatments as $treatment) {
                if ($treatment->getEtat() == 'En cours' && $this->isTreatmentContains($treatment, $currentDate, $endDate)) {

                    // Calculer la durée du traitement
                    $durationInSeconds = $treatment->getDateFin()->getTimestamp() - $treatment->getDateDebut()->getTimestamp();

                    if ($treatment->getDateFin() > $endDate) {
                        $durationUntilEndDate = $endDate->getTimestamp() - $treatment->getDateDebut()->getTimestamp();
                        $durationInSeconds = max(0, $durationUntilEndDate);
                    }

                    $startDateTimestamp = $treatment->getDateDebut()->getTimestamp();
                    $endDateTimestamp = $treatment->getDateFin()->getTimestamp();
                    if ($startDateTimestamp < $currentDate->getTimestamp()) {
                        $startDateTimestamp = $currentDate->getTimestamp();
                        $durationInSeconds = $endDateTimestamp - $startDateTimestamp;
                    }

                    // Ajouter la durée au compteur approprié pour chaque process
                    $totalDurationDayByPool += min($durationInSeconds, $secondsInDay);
                    $totalDurationWeekByPool += min($durationInSeconds, $secondsInWeek);
                    $totalDurationMonthByPool += min($durationInSeconds, $secondsInMonth);
                    $totalDurationYearByPool += min($durationInSeconds, $secondsInYear);

                    // Convertir la durée totale en heures
                    $totalDurationDayByPoolInHours = $totalDurationDayByPool / 3600;
                    $totalDurationWeekByPoolInHours = $totalDurationWeekByPool / 3600;
                    $totalDurationMonthByPoolInHours = $totalDurationMonthByPool / 3600;
                    $totalDurationYearByPoolInHours = $totalDurationYearByPool / 3600;

                    // Récupérer les VM utilisées dans ce traitement qui appartiennent à la pool
                    $vmsInTreatment = $treatment->getIdVm();
                    foreach ($vmsInTreatment as $vm) {
                        if (in_array($vm->getNomVm(), $nomVmArray)) {
                            $vmUse[] = $vm->getNomVm();
                        }
                    }


                }
            }

            // Les VM disponibles sont celles qui ne sont pas utilisées dans cette pool
            $vmAvailable = array_diff($nomVmArray, $vmUse);

            if(empty($vmAvailable) && $treatment->getDateDebut() <= $currentDate && $treatment->getDateFin() >= $endDate) {
                    $percentageDay = 100;
                    $percentageWeek = 100;
                    $percentageMonth = 100;
                    $percentageYear = 100;
            } 
            else if (empty($vmUse)) {
                $percentageDay = 0;
                $percentageWeek = 0;
                $percentageMonth = 0;
                $percentageYear = 0;
            }
            else 
            {
                if (count($vmAvailable) > 0) {
                    $percentageDay0 = min(($totalDurationDayByPool / $secondsInDay) * 100, 100);
                    $percentageDay = $percentageDay0 / count($vmAvailable);
    
                    $percentageWeek0 = min(($totalDurationWeekByPool / $secondsInWeek) * 100, 100);
                    $percentageWeek = $percentageWeek0 / count($vmAvailable);
    
                    $percentageMonth0 = min(($totalDurationMonthByPool / $secondsInMonth) * 100, 100);
                    $percentageMonth = $percentageMonth0 / count($vmAvailable);
    
                    $percentageYear0 = min(($totalDurationYearByPool / $secondsInYear) * 100, 100);  
                    $percentageYear = $percentageYear0 / count($vmAvailable);
                } else {
                    $percentageDay = min(($totalDurationDayByPool / $secondsInDay) * 100, 100);
                    $percentageWeek = min(($totalDurationWeekByPool / $secondsInWeek) * 100, 100);
                    $percentageMonth = min(($totalDurationMonthByPool / $secondsInMonth) * 100, 100);
                    $percentageYear = min(($totalDurationYearByPool / $secondsInYear) * 100, 100);  
                }
            }

            // Stocker les données de la pool dans le tableau $poolData
            $poolData[] = [
                'nom_pool' => $pool->getNomPool(),
                'percentageDay' => $percentageDay,
                'percentageWeek' => $percentageWeek,
                'percentageMonth' => $percentageMonth,
                'percentageYear' => $percentageYear,
                'totalDurationDayByPoolInHours' => $totalDurationDayByPoolInHours,
                'totalDurationWeekByPoolInHours' => $totalDurationWeekByPoolInHours,
                'totalDurationMonthByPoolInHours' => $totalDurationMonthByPoolInHours,
                'totalDurationYearByPoolInHours' => $totalDurationYearByPoolInHours,
                'vm_available' => $vmAvailable,
                'vm_used' => $vmUse,
            ];
        }

        return new JsonResponse($poolData);
    }



    private function isTreatmentContains(\App\Entity\Treatment $treatment, \DateTime $currentDate, \DateTime $endDate): bool
    {
        // Assuming that the treatment contains a start date and end date properties
        $startDate = $treatment->getDateDebut();
        $endDateTreatment = $treatment->getDateFin();

        // Check if the treatment is ongoing at any point within the specified date range
        return ($startDate <= $endDate && $endDateTreatment >= $currentDate);
    }

}

