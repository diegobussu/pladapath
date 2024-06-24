<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Entity\Logs;
use Doctrine\ORM\EntityManagerInterface;

class ContactController extends AbstractController
{

    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    

    public function index(UserInterface $user): Response {

        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('contact/index.html.twig',
            [
                'num_agent' => $num_agent,
                'prenom_agent' => $prenom_agent,
                'nom_agent' => $nom_agent,
            ]
        );
    }


    public function contactForm(Request $request, MailerInterface $mailer, UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        // recupération des éléments du formulaire
        $subjectValue  = $request->request->get('subject');
        $subjectText = '';

        switch ($subjectValue) {
            case '1':
                $subjectText = 'Problème concernant le calendrier';
                break;
            case '3':
                $subjectText = 'Problème concernant un traitement';
                break;
            case '4':
                $subjectText = 'Problème concernant un affichage';
                break;
            case '5':
                $subjectText = 'Problème concernant une connexion';
                break;
            case '6':
                $subjectText = 'Problème concernant un ajout de champs';
                break;
            case '7':
                $subjectText = 'Problème concernant une suppression de champs';
                break;
            case '8':
                $subjectText = 'Problème concernant une modification de champs';
                break;
            case '9':
                $subjectText = 'Problème concernant un filtre';
                break;
            case '10':
                $subjectText = 'Proposition à propos d\'une fonctionnalité';
                break;
            case '11':
                $subjectText = 'Autre';
                break;
            default:
                // Gérer le cas où la valeur n'est pas reconnue
                break;
        }

        $messageText = $request->request->get('message');
        
        $autor = $user->getNom() . ' ' . $user->getPrenom() .  '.';

        try {
            $email = (new Email())
                ->from('developpement-informatique.cpam-val-de-marne@assurance-maladie.fr')
                ->to('developpement-informatique.cpam-val-de-marne@assurance-maladie.fr')
                ->subject('PLADAPATH - Formulaire de contact')
                ->text('Sujet : ' . $subjectText . "\n\n" . 'Message : ' . $messageText . "\n\n" . "Cordialement," . "\n\n" . $autor);
    
            $mailer->send($email);
    
            $this->addFlash('success', 'Demande envoyé !');

            
            // Enregistrement des logs
            $logs = new Logs();
            $logs->setAuteur($user->getNom() . ' ' . $user->getPrenom());
            $logs->setDate(new \DateTime());
            $logs->setAction('Envoi d\'un mail via le formulaire de contact.');
    
            $entityManager->persist($logs);
    
            $entityManager->flush();

        } catch (TransportExceptionInterface $e) {

            $this->addFlash('error', 'Erreur. Demande non envoyé !');
        }

        return $this->redirectToRoute('contact');
    }
}