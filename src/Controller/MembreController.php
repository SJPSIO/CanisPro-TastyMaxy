<?php

namespace App\Controller;

use App\Entity\Chien;
use App\Form\ChienType;
use App\Repository\ChienRepository;
use App\Repository\ProprietaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/membre')]
final class MembreController extends AbstractController
{
    #[Route('/dashboard', name: 'app_membre_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('membre/dashboard.html.twig');
    }

    #[Route('/profil', name: 'app_membre_profil')]
    public function profil(): Response
    {
        $proprietaire = [
            'nom' => 'Phoenix',
            'prenom' => 'Joachin',
            'telephone' => '06 12 34 56 78',
            'email' => 'Joachin.phoenix@email.fr',
        ];

        return $this->render('membre/profil.html.twig', [
            'proprietaire' => $proprietaire,
        ]);
    }

    #[Route('/chiens', name: 'app_membre_chiens')]
    public function chiens(ChienRepository $chienRepository): Response
    {
        $chiens = $chienRepository->findAll();

        return $this->render('membre/chiens/index.html.twig', [
            'chiens' => $chiens,
        ]);
    }

    #[Route('/chiens/nouveau', name: 'app_membre_chien_new')]
    public function chienNew(
        Request $request,
        EntityManagerInterface $entityManager,
        ProprietaireRepository $proprietaireRepository
    ): Response {

        $chien = new Chien();

        $form = $this->createForm(ChienType::class, $chien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $proprietaires = $proprietaireRepository->findAll();

        if (empty($proprietaires)) {
            throw $this->createNotFoundException('Aucun propriétaire en base.');
        }

        $proprietaire = $proprietaires[0];

        $chien->setProprietaire($proprietaire);

        $entityManager->persist($chien);
        $entityManager->flush();

        return $this->redirectToRoute('app_membre_chiens');
    }

        return $this->render('membre/chiens/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/chiens/{id}/edit', name: 'app_membre_chien_edit', requirements: ['id' => '\d+'])]
    public function chienEdit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ChienRepository $chienRepository
    ): Response {

        $chien = $chienRepository->find($id);

        if (!$chien) {
            throw $this->createNotFoundException('Chien introuvable');
        }

        $form = $this->createForm(ChienType::class, $chien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_membre_chiens');
        }

        return $this->render('membre/chiens/edit.html.twig', [
            'form' => $form->createView(),
            'chien' => $chien
        ]);
    }

    #[Route('/chiens/{id}/supprimer', name: 'app_membre_chien_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function chienDelete(
        int $id,
        ChienRepository $chienRepository,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $chien = $chienRepository->find($id);

        if (!$chien) {
            throw $this->createNotFoundException('Chien introuvable');
        }

        if (!$chien->getInscriptions()->isEmpty()) {
            $this->addFlash('error', 'Impossible de supprimer ce chien car il est inscrit à une ou plusieurs séances.');
            return $this->redirectToRoute('app_membre_chiens');
        }

        $entityManager->remove($chien);
        $entityManager->flush();

        $this->addFlash('success', 'Le chien a bien été supprimé.');

        return $this->redirectToRoute('app_membre_chiens');
    }

    #[Route('/seances', name: 'app_membre_seances')]
    public function seances(): Response
    {
        $seances = [
            ['id' => 1, 'cours' => 'Obéissance débutant', 'type' => 'Collectif', 'date' => '2025-04-05', 'heure' => '10:00', 'lieu' => 'Terrain principal', 'places' => 8, 'max' => 15],
            ['id' => 2, 'cours' => 'Cours chiot', 'type' => 'Collectif', 'date' => '2025-04-07', 'heure' => '14:00', 'lieu' => 'Terrain A', 'places' => 3, 'max' => 15],
            ['id' => 3, 'cours' => 'Agility débutant', 'type' => 'Collectif', 'date' => '2025-04-12', 'heure' => '09:00', 'lieu' => 'Terrain agility', 'places' => 12, 'max' => 15],
            ['id' => 4, 'cours' => 'Éducation individuelle', 'type' => 'Individuel', 'date' => '2025-04-10', 'heure' => '11:00', 'lieu' => 'Terrain principal', 'places' => 1, 'max' => 1],
        ];

        $chiens = [
            ['id' => 1, 'nom' => 'Rocky'],
            ['id' => 2, 'nom' => 'Luna'],
        ];

        return $this->render('membre/seances.html.twig', [
            'seances' => $seances,
            'chiens' => $chiens,
        ]);
    }

    #[Route('/inscriptions', name: 'app_membre_inscriptions')]
    public function inscriptions(): Response
    {
        $inscriptions = [
            ['id' => 1, 'cours' => 'Obéissance débutant', 'chien' => 'Rocky', 'date_seance' => '2025-04-05', 'heure' => '10:00', 'lieu' => 'Terrain principal', 'date_inscription' => '2025-03-10'],
            ['id' => 2, 'cours' => 'Cours chiot', 'chien' => 'Luna', 'date_seance' => '2025-04-07', 'heure' => '14:00', 'lieu' => 'Terrain A', 'date_inscription' => '2025-03-12'],
        ];

        return $this->render('membre/inscriptions.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }
}