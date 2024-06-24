<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\UpdateMessageType;
use App\Entity\Logs;
use App\Repository\LogsRepository;

class MessageController extends AbstractController
{    
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }


    
    public function createMessage(UserInterface $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();

        $message->setDateCreation(new \DateTime());
        $message->setAuteur($user->getNom() . ' ' . $user->getPrenom());

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $titre = $message->getTitre();

            if (trim($titre) === '') {
                $this->addFlash('error', 'Le titre ne peut pas être vide.');
                return $this->redirectToRoute('message_create'); 
            }

            $contenu = $message->getContenu();

            if (trim($contenu) === '') {
                $this->addFlash('error', 'Le contenu ne peut pas être vide.');
                return $this->redirectToRoute('message_create'); 
            }


            $dateDebut = $message->getDateDebut();
            $dateFin = $message->getDateFin();
            
            if ($dateFin <= $dateDebut) { 
                $this->addFlash('error', 'La date de fin ne peut pas être inférieure ou égale à la date de début.');
                
                return $this->redirectToRoute('message_create'); 
            }
            

            $entityManager->persist($message);
            $entityManager->flush();

            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Création d\'une annonce : ' . $message->getTitre() . ', contenu : '. $message->getContenu());

            $entityManager->persist($logs);
            $entityManager->flush();

            $this->addFlash('success', 'Annonce ajoutée.');
            
            return $this->redirectToRoute('setting');
        }

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('message/create.message.html.twig', [
            'num_agent' => $num_agent,
            'prenom_agent' => $prenom_agent,
            'nom_agent' => $nom_agent,
            'form' => $form->createView(),
        ]);
    }

    public function deleteMessage(UserInterface $user, $id, EntityManagerInterface $entityManager): Response
    {
        $message = $entityManager->getRepository(Message::class)->find($id);
    
        if (!$message) {
            $this->addFlash('error', 'Message non trouvé.');
            return $this->redirectToRoute('setting');  
        }

        $currentUser = $user->getNom() . ' ' . $user->getPrenom();

        if ($currentUser !== $message->getAuteur()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer une annonce dont vous n\'êtes pas l\'auteur.');
            return $this->redirectToRoute('setting');
        }

        // Enregistrement des logs
        $logs = new Logs();
        $logs->setAuteur($currentUser);
        $logs->setDate(new \DateTime());
        $logs->setAction('Suppression d\'une annonce : ' . $message->getTitre() . ', contenu : '. $message->getContenu());
    
        $entityManager->persist($logs);

        $entityManager->remove($message);
        $entityManager->flush();

        $this->addFlash('success', 'Annonce supprimée.');

        return $this->redirectToRoute('setting');  
    }


    public function editMessage(UserInterface $user, Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UpdateMessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentUser = $user->getNom() . ' ' . $user->getPrenom();

            if ($currentUser !== $message->getAuteur()) {
                $this->addFlash('error', 'Vous ne pouvez pas modifier une annonce dont vous n\'êtes pas l\'auteur.');
                return $this->redirectToRoute('setting');
            }
            
            $datedebut = $message->getDateDebut();
            $datefin = $message->getDateFin();
            $titre = $message->getTitre();
            $contenu = $message->getContenu();
            $priorite = $message->getPriorite();

            // Récupération des nouvelles valeurs du formulaire
            $originalDateDebut = $entityManager->getUnitOfWork()->getOriginalEntityData($message)['date_debut'];
            $originalDateFin = $entityManager->getUnitOfWork()->getOriginalEntityData($message)['date_fin'];
            $originalTitre = $entityManager->getUnitOfWork()->getOriginalEntityData($message)['titre'];
            $originalContenu = $entityManager->getUnitOfWork()->getOriginalEntityData($message)['contenu'];
            $originalPriorite = $entityManager->getUnitOfWork()->getOriginalEntityData($message)['priorite'];

            // Comparaison des valeurs
            if ($originalTitre === $titre && $originalContenu === $contenu && $originalPriorite === $priorite && $originalDateDebut === $datedebut && $originalDateFin === $datefin) {
                $this->addFlash('error', 'Aucune modification détectée.');
                return $this->redirectToRoute('message_edit', ['id' => $message->getId()]);
            }

            if (trim($titre) === '') {
                $this->addFlash('error', 'Le titre ne peut pas être vide.');
                return $this->redirectToRoute('message_edit', ['id' => $message->getId()]);
            }

            if (trim($contenu) === '') {
                $this->addFlash('error', 'Le contenu ne peut pas être vide.');
                return $this->redirectToRoute('message_edit', ['id' => $message->getId()]);
            }
            
            $dateDebut = $message->getDateDebut();
            $dateFin = $message->getDateFin();
            
            
            if ($dateFin <= $dateDebut) { 
                $this->addFlash('error', 'La date de fin ne peut pas être inférieure ou égale à la date de début.');
                return $this->redirectToRoute('message_edit', ['id' => $message->getId()]);
            }
            
        
            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($currentUser);
            $logs->setDate(new \DateTime());
            $logs->setAction('Modification d\'une annonce : ' . $message->getTitre() . ', contenu : '. $message->getContenu());
    
            $entityManager->persist($logs);

            $entityManager->flush();

            $this->addFlash('success', 'Annonce modifiée.');
            return $this->redirectToRoute('setting');
        }

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('message/edit.message.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }





    public function allLogsHistory (LogsRepository $LogsRepository, UserInterface $user): Response
    { 
        $listAllLogs = $LogsRepository->findAll();

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('message/logs.message.html.twig', [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
            'listAllLogs' => $listAllLogs
        ]);
    }
}
