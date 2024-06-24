<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UpdatePoolVmType;
use App\Entity\PoolVm;
use App\Form\PoolVmType;
use App\Repository\PoolVmRepository;
use App\Entity\Logs;

class PoolVmController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }


    
    public function createPoolVm(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $poolVm = new PoolVm();
        $form = $this->createForm(PoolVmType::class, $poolVm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //voir si la Pool existe
            $existingPoolVm = $entityManager->getRepository(PoolVm::class)->findOneBy(['nom_pool' => $poolVm->getNomPool()]);
            if ($existingPoolVm) {
                $this->addFlash('error', 'Ce nom est déjà utilisé.');

                return $this->redirectToRoute('poolvm_create');

            } 

            $nomPool = $poolVm->getNomPool();

            if (trim($nomPool) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('poolvm_create');
            }

            else {
                $entityManager->persist($poolVm);
                $entityManager->flush();

                // Enregistrement des logs
                $logs = new Logs();
                $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                $logs->setDate(new \DateTime());
                $logs->setAction('Création d\'une pool : ' . $poolVm->getNomPool());

                $entityManager->persist($logs);
                $entityManager->flush();

                $this->addFlash('success', 'Pool ajouté.');

                return $this->redirectToRoute('setting');
            }
        }

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('pool_vm/create.poolvm.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'form' => $form->createView(),
        ]);
    }


    public function deletePoolVm(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    {
        $poolvm = $entityManager->getRepository(PoolVm::class)->find($id);
    
        if (!$poolvm) {
            $this->addFlash('error', 'Pool non trouvé.');
            return $this->redirectToRoute('setting');  
        }

        // Vérifier si la Pool appartient à un traitement
        $traitements = $poolvm->getIdTraitement();

        if ($traitements->count() > 0) {
            $this->addFlash('error', 'Cette pool appartient à des traitements. Veuillez supprimer les traitements avant de supprimer la pool.');
            return $this->redirectToRoute('setting');
        }


        // Vérifier si la Pool contient des VM
        $vmsInPool = $poolvm->getNomVm();

        if ($vmsInPool->count() > 0) {
            // La Pool contient des VM, ajouter un message flash
            $this->addFlash('error', 'Cette pool contient une ou plusieurs VM. Veuillez toutes les supprimer avant de supprimer la pool.');
            return $this->redirectToRoute('setting');
        }
    
        // Enregistrement des logs
        $logs = new Logs();
        $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
        $logs->setDate(new \DateTime());
        $logs->setAction('Suppression d\'une pool : ' . $poolvm->getNomPool());

        $entityManager->persist($logs);

        $entityManager->remove($poolvm);
        $entityManager->flush();

        $this->addFlash('success', 'Pool supprimé.');
        
        return $this->redirectToRoute('setting');  
    }


    public function editPoolVm(UserInterface $user, Request $request, PoolVm $poolvm, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(UpdatePoolVmType::class, $poolvm);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $nomPool = $poolvm->getNomPool();
                
                // Récupération des nouvelles valeurs du formulaire
                $originalNomPool = $entityManager->getUnitOfWork()->getOriginalEntityData($poolvm)['nom_pool'];

                // Comparaison des valeurs
                if ($originalNomPool === $nomPool) {
                    $this->addFlash('error', 'Aucune modification détectée.');
                    return $this->redirectToRoute('poolvm_edit', ['id' => $poolvm->getId()]);
                }

                if (trim($nomPool) === '') {
                    $this->addFlash('error', 'Le nom ne peut pas être vide.');
                    return $this->redirectToRoute('poolvm_edit', ['id' => $poolvm->getId()]);
                }

                // Enregistrement des logs
                $logs = new Logs();
                $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                $logs->setDate(new \DateTime());
                $logs->setAction('Modification d\'une pool : ' . $poolvm->getNomPool());

                $entityManager->persist($logs);

                $entityManager->flush();

                $this->addFlash('success', 'Pool modifié.');

                return $this->redirectToRoute('setting');
            }

            $num_agent = $user->getNumeroAgent();
            $nom_agent = $user->getNom();
            $prenom_agent = $user->getPrenom();

            return $this->render('pool_vm/edit.poolvm.html.twig', [
                'num_agent' => $num_agent,
                'nom_agent' => $nom_agent,
                'prenom_agent' => $prenom_agent,
                'poolvm' => $poolvm,
                'form' => $form->createView(),
            ]);
    }



    
    public function getCaisseExploitNames(PoolVmRepository $PoolVmRepository)
    {
        $caisseExploitNames = $PoolVmRepository->findAllPoolVmNames();

        return new JsonResponse($caisseExploitNames);
    }
}
