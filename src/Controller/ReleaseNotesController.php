<?php

namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReleaseNotesController extends AbstractController
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
    }
    

    public function index(UserInterface $user): Response
    {
        $num_agent = $user->getNumeroAgent();
        $nom_agent = $user->getNom();
        $prenom_agent = $user->getPrenom();

        return $this->render('release_notes/index.html.twig',
        [
            'num_agent' => $num_agent,
            'nom_agent' => $nom_agent,
            'prenom_agent' => $prenom_agent,
        ]
    );
    }
}
