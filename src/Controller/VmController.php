<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Vm;
use App\Repository\PoolVmRepository;
use App\Form\VmType;
use App\Form\UpdateVmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Logs;
use Symfony\Component\HttpFoundation\JsonResponse;


class VmController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }


    public function createVm(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {

        $vm = new Vm();
        $form = $this->createForm(VmType::class, $vm);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $existingVm = $entityManager->getRepository(Vm::class)->findOneBy(['nom_vm' => $vm->getNomVm()]);
            $nomVm = $vm->getNomVm();
            
            if ($existingVm) {
                $this->addFlash('error', 'Ce nom est déjà utilisé.');
                return $this->redirectToRoute('vm_create');
            } 

            $nomVm = $vm->getNomVm();
            
            if (trim($nomVm) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('vm_create');
            }
            
            $pool = $vm->getPoolVm();
            $vmCount = $entityManager->getRepository(Vm::class)->count(['poolVm' => $pool]);

            // Si le nombre de VMs dépasse 10, afficher un message d'erreur
            if ($vmCount >= 10) {
                $this->addFlash('error', 'Le pool ' . $pool->getNomPool() . ' a déjà atteint sa capacité maximale de VMs.');
                return $this->redirectToRoute('vm_create');
            }

            else {
                if ($vm->getNomVm() !== null && $vm->getPoolVm() !== null) {
                    $entityManager->persist($vm);
        
                    // Enregistrement des logs
                    $logs = new Logs();
                    $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                    $logs->setDate(new \DateTime());
                    $logs->setAction('Création d\'une vm : ' . $vm->getNomVm() . 
                                    ' Pool associé : ' . $vm->getPoolVm()->getNomPool()
                    );
        
                    $entityManager->persist($logs);
                }
            }
        
            $entityManager->flush();
        
            $this->addFlash('success', 'VM ajoutée.');
            return $this->redirectToRoute('setting');
        }
        

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('vm/create.vm.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            
            'form' => $form->createView(),
        ]);
    }


    
    public function deleteVm(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    {
        $vm = $entityManager->getRepository(Vm::class)->find($id);

        // Vérifier si la VM existe
        if (!$vm) {
            $this->addFlash('error', 'VM non trouvé.');
            return $this->redirectToRoute('setting');  
        }

        // Vérifier si la VM appartient à un traitement
        $traitements = $vm->getIdTraitement();

        if ($traitements->count() > 0) {
            $this->addFlash('error', 'Cette VM appartient à des traitements. Veuillez supprimer les traitements avant de supprimer la VM.');
            return $this->redirectToRoute('setting');
        }

        // Enregistrement des logs
        $logs = new Logs();
        $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
        $logs->setDate(new \DateTime());
        $logs->setAction('Suppression d\'une vm : ' . $vm->getNomVm() .
                        ' Pool associé : ' . $vm->getPoolVm()->getNomPool()
                        );

        $entityManager->persist($logs);

        $entityManager->remove($vm);
        $entityManager->flush();
    
        $this->addFlash('success', 'VM supprimée.');

        return $this->redirectToRoute('setting');  
    }



    public function editVm(UserInterface $user, Request $request, Vm $vm, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(UpdateVmType::class, $vm);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $nomVm = $vm->getNomVm();

                // Récupération des nouvelles valeurs du formulaire
                $originalNomVm = $entityManager->getUnitOfWork()->getOriginalEntityData($vm)['nom_vm'];

                // Comparaison des valeurs
                if ($originalNomVm === $nomVm) {
                    $this->addFlash('error', 'Aucune modification détectée.');
                    return $this->redirectToRoute('vm_edit', ['id' => $vm->getId()]);
                }

                if (trim($nomVm) === '') {
                    $this->addFlash('error', 'Le nom ne peut pas être vide.');
                    return $this->redirectToRoute('vm_edit', ['id' => $vm->getId()]);
                }

                // Enregistrement des logs
                $logs = new Logs();
                $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                $logs->setDate(new \DateTime());
                $logs->setAction('Modification d\'une vm : ' . $vm->getNomVm() .
                                ' Pool associé : ' . $vm->getPoolVm()->getNomPool()
                                );
                $entityManager->persist($logs);

                $entityManager->flush();

                $this->addFlash('success', 'Vm modifiée.');

                return $this->redirectToRoute('setting');
            }
            
            $num_agent = $user->getNumeroAgent();
            $nom_agent = $user->getNom();
            $prenom_agent = $user->getPrenom();

            return $this->render('vm/edit.vm.html.twig', [
                'num_agent' => $num_agent,
                'nom_agent' => $nom_agent,
                'prenom_agent' => $prenom_agent,
                'vm' => $vm,
                'form' => $form->createView(),
            ]);
        }


    public function getPools(PoolVmRepository $poolVmRepository): JsonResponse
    {
        $pools = $poolVmRepository->findAll(); 

        $poolData = [];
        foreach ($pools as $pool) {
            $poolData[] = [
                'id' => $pool->getId(),
                'nom_pool' => $pool->getNomPool(),
            ];
        }

        return $this->json($poolData);
    }



}
