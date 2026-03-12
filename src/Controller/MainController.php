<?php

namespace App\Controller;

use App\Repository\ChienRepository;
use App\Repository\ProprietaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function test(ChienRepository $chienRepo, ProprietaireRepository $proprioRepo): Response
    {
        // On récupère tout ce qu'il y a en base
        $lesChiens = $chienRepo->findAll();
        $lesProprios = $proprioRepo->findAll();

        return $this->render('main/test.html.twig', [
            'chiens' => $lesChiens,
            'proprietaires' => $lesProprios,
        ]);
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}

