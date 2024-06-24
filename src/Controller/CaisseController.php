<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UpdateCaisseType;
use App\Form\CaisseType;
use App\Entity\Caisse;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Logs;

class CaisseController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    
    public function createCaisse(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $roleUser = $user->getRoles();

        if (in_array('ADMINISTRATEUR', $roleUser)) {
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas administrateur.');
            return $this->redirectToRoute('setting');
        }
        
        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        $caisse = new Caisse();
        $form = $this->createForm(CaisseType::class, $caisse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $existingNomCaisse = $entityManager->getRepository(Caisse::class)->findOneBy(['nom_caisse' => $caisse->getNomCaisse()]);

            if ($existingNomCaisse) 
            {
                $this->addFlash('error', 'Ce nom est déjà utilisé.');
                return $this->redirectToRoute('caisse_create');
            } 

            $existingNumCaisse = $entityManager->getRepository(Caisse::class)->findOneBy(['num_caisse' => $caisse->getNumCaisse()]);

            if ($existingNumCaisse) 
            {
                $this->addFlash('error', 'Ce numéro est déjà utilisé.');
                return $this->redirectToRoute('caisse_create');
            } 

            $nomCaisse = $caisse->getNomCaisse();

            if (trim($nomCaisse) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('caisse_create');
            }

            $numCaisse = $caisse->getNumCaisse();

            if (trim($numCaisse) === '') {
                $this->addFlash('error', 'Le numéro ne peut pas être vide.');
                return $this->redirectToRoute('caisse_create');
            }

            else 
            {
                $entityManager->persist($caisse);
                $entityManager->flush();

                // Enregistrement des logs
                $logs = new Logs();
                $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
                $logs->setDate(new \DateTime());
                $logs->setAction('Création d\'une caisse : ' . $caisse->getNumCaisse() . ' - ' .  $caisse->getNomCaisse());

                $entityManager->persist($logs);
                $entityManager->flush();

                $this->addFlash('success', 'Caisse ajoutée.');

                return $this->redirectToRoute('setting'); 
            }
        }

        return $this->render('caisse/create.caisse.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'form' => $form->createView(),
        ]);
    }


    public function deleteCaisse(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    {
        $roleUser = $user->getRoles();

        if (in_array('ADMINISTRATEUR', $roleUser)) {
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas administrateur.');
            return $this->redirectToRoute('setting');
        }

        $caisse = $entityManager->getRepository(Caisse::class)->find($id);
    
        if (!$caisse) {
            $this->addFlash('error', 'Caisse non trouvé.');
            return $this->redirectToRoute('setting');  
        }

        
        // Vérifier si la caisse appartient à un traitement
        $traitements = $caisse->getTraitements();

        if ($traitements->count() > 0) {
            $this->addFlash('error', 'Cette caisse appartient à un/des traitement(s). Veuillez supprimer le/les traitement(s) avant de supprimer la caisse.');
            return $this->redirectToRoute('setting');
        }

        // Enregistrement des logs
        $logs = new Logs();
        $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
        $logs->setDate(new \DateTime());
        $logs->setAction('Suppression d\'une caisse : ' . $caisse->getNumCaisse() . ' - ' .  $caisse->getNomCaisse());
    
        $entityManager->persist($logs);
            
        $entityManager->remove($caisse);
        $entityManager->flush();

        $this->addFlash('success', 'Caisse supprimée.');

        return $this->redirectToRoute('setting');  
    }


    public function editCaisse(UserInterface $user, Request $request, Caisse $caisse, EntityManagerInterface $entityManager): Response
    {
        $roleUser = $user->getRoles();

        if (in_array('ADMINISTRATEUR', $roleUser)) {
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas administrateur.');
            return $this->redirectToRoute('setting');
        }

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        $form = $this->createForm(UpdateCaisseType::class, $caisse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numCaisse = $caisse->getNumCaisse();
            $nomCaisse = $caisse->getNomCaisse();

            // Récupération des nouvelles valeurs du formulaire
            $originalNomCaisse = $entityManager->getUnitOfWork()->getOriginalEntityData($caisse)['nom_caisse'];
            $originalNumCaisse = $entityManager->getUnitOfWork()->getOriginalEntityData($caisse)['num_caisse'];

            // Comparaison des valeurs
            if ($originalNomCaisse === $nomCaisse && $originalNumCaisse === $numCaisse) {
                $this->addFlash('error', 'Aucune modification détectée.');
                return $this->redirectToRoute('caisse_edit', ['id' => $caisse->getId()]);
            }

            if (trim($nomCaisse) === '') {
                $this->addFlash('error', 'Le nom ne peut pas être vide.');
                return $this->redirectToRoute('caisse_edit', ['id' => $caisse->getId()]);
            }

            if (trim($numCaisse) === '') {
                $this->addFlash('error', 'Le numéro ne peut pas être vide.');
                return $this->redirectToRoute('caisse_edit', ['id' => $caisse->getId()]);
            }

            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Modification d\'une caisse : ' . $caisse->getNumCaisse() . ' - ' .  $caisse->getNomCaisse());
    
            $entityManager->persist($logs);

            $entityManager->flush();

            $this->addFlash('success', 'Caisse modifiée.');

            return $this->redirectToRoute('setting');
        }

        return $this->render('caisse/edit.caisse.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'caisse' => $caisse,
            'form' => $form->createView(),
        ]);
    }

}
