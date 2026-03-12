<?php

namespace App\Controller;

use App\Repository\ChienRepository;        // Import important !
use App\Repository\ProprietaireRepository; // Import important !
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(ChienRepository $chienRepo, ProprietaireRepository $proprioRepo): Response
    {
        // On récupère tout ce qu'il y a en base
        $lesChiens = $chienRepo->findAll();
        $lesProprios = $proprioRepo->findAll();

        return $this->render('main/index.html.twig', [
            'chiens' => $lesChiens,
            'proprietaires' => $lesProprios,
        ]);
    }
}