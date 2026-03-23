<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use App\Repository\SeanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CoursController extends AbstractController
{
    #[Route('/cours', name: 'app_cours_index')]
    public function index(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll();

        return $this->render('cours/index.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/{id}', name: 'app_cours_show', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        CoursRepository $coursRepository,
        SeanceRepository $seanceRepository
    ): Response {
        $cours = $coursRepository->find($id);

        if (!$cours) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        $seances = $seanceRepository->findBy(
            ['cours' => $cours],
            ['dateHeure' => 'ASC']
        );

        return $this->render('cours/show.html.twig', [
            'cours' => $cours,
            'seances' => $seances,
        ]);
    }
}