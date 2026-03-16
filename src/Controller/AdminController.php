<?php

namespace App\Controller;

use App\Repository\ChienRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ProprietaireRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Utilisateur;
use App\Entity\Proprietaire;
use App\Entity\Chien;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    private function getStatsFactices(): array
    {
        return [
            'nb_utilisateurs'  => 24,
            'nb_chiens'        => 31,
            'nb_cours'         => 6,
            'nb_seances'       => 18,
            'nb_inscriptions'  => 47,
        ];
    }

    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'stats' => $this->getStatsFactices(),
        ]);
    }

    // ---- Utilisateurs ----

    #[Route('/utilisateurs', name: 'app_admin_utilisateurs')]
    public function utilisateurs(UtilisateurRepository $utilisateurRepository): Response
    {
        // Récupération de tous les utilisateurs depuis la BDD
        $utilisateurs = $utilisateurRepository->findAll();

        return $this->render('admin/utilisateurs/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    // Suppression (calqué sur le tp2)
    #[Route('/admin/utilisateur/supprimer/{id}', name: 'admin_utilisateur_supprimer', methods: ['POST'])]
    public function supprimerUtilisateur(
        Utilisateur $utilisateur,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_utilisateurs');
    }
    

    // ---- Cours ----

    #[Route('/cours', name: 'app_admin_cours')]
    public function cours(): Response
    {
        $cours = [
            ['id' => 1, 'titre' => 'Éducation individuelle', 'type' => 'Individuel', 'niveau' => 'Tous niveaux', 'prix' => 65.00, 'nb_seances' => 4],
            ['id' => 2, 'titre' => 'Cours chiot',            'type' => 'Collectif',  'niveau' => 'Chiot',       'prix' => 40.00, 'nb_seances' => 3],
            ['id' => 3, 'titre' => 'Obéissance débutant',    'type' => 'Collectif',  'niveau' => 'Débutant',    'prix' => 35.00, 'nb_seances' => 5],
            ['id' => 4, 'titre' => 'Obéissance confirmé',    'type' => 'Collectif',  'niveau' => 'Confirmé',    'prix' => 38.00, 'nb_seances' => 3],
            ['id' => 5, 'titre' => 'Agility débutant',       'type' => 'Collectif',  'niveau' => 'Débutant',    'prix' => 42.00, 'nb_seances' => 2],
            ['id' => 6, 'titre' => 'Sociabilisation',        'type' => 'Collectif',  'niveau' => 'Chiot',       'prix' => 32.00, 'nb_seances' => 1],
        ];

        return $this->render('admin/cours/index.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/cours/nouveau', name: 'app_admin_cours_new')]
    public function coursNew(): Response
    {
        return $this->render('admin/cours/new.html.twig');
    }

    #[Route('/cours/{id}/edit', name: 'app_admin_cours_edit', requirements: ['id' => '\d+'])]
    public function coursEdit(int $id): Response
    {
        $cours = ['id' => $id, 'titre' => 'Obéissance débutant', 'type' => 'Collectif', 'niveau' => 'Débutant', 'description' => 'Description du cours...', 'prix' => 35.00];

        return $this->render('admin/cours/edit.html.twig', [
            'cours' => $cours,
        ]);
    }

    // ---- Séances ----

    #[Route('/seances', name: 'app_admin_seances')]
    public function seances(): Response
    {
        $seances = [
            ['id' => 1, 'cours' => 'Obéissance débutant', 'date' => '2025-04-05', 'heure' => '10:00', 'lieu' => 'Terrain principal', 'nb_inscriptions' => 7,  'max' => 15],
            ['id' => 2, 'cours' => 'Cours chiot',          'date' => '2025-04-07', 'heure' => '14:00', 'lieu' => 'Terrain A',         'nb_inscriptions' => 12, 'max' => 15],
            ['id' => 3, 'cours' => 'Agility débutant',    'date' => '2025-04-12', 'heure' => '09:00', 'lieu' => 'Terrain agility',    'nb_inscriptions' => 3,  'max' => 15],
            ['id' => 4, 'cours' => 'Éducation individuelle','date'=> '2025-04-10', 'heure' => '11:00', 'lieu' => 'Terrain principal',  'nb_inscriptions' => 1,  'max' => 1],
            ['id' => 5, 'cours' => 'Sociabilisation',      'date' => '2025-04-15', 'heure' => '15:00', 'lieu' => 'Terrain B',         'nb_inscriptions' => 15, 'max' => 15],
        ];

        return $this->render('admin/seances/index.html.twig', [
            'seances' => $seances,
        ]);
    }

    #[Route('/seances/nouvelle', name: 'app_admin_seance_new')]
    public function seanceNew(): Response
    {
        $cours = [
            ['id' => 1, 'titre' => 'Éducation individuelle'],
            ['id' => 2, 'titre' => 'Cours chiot'],
            ['id' => 3, 'titre' => 'Obéissance débutant'],
            ['id' => 4, 'titre' => 'Obéissance confirmé'],
            ['id' => 5, 'titre' => 'Agility débutant'],
            ['id' => 6, 'titre' => 'Sociabilisation'],
        ];

        return $this->render('admin/seances/new.html.twig', [
            'cours' => $cours,
        ]);
    }

    #[Route('/seances/{id}/edit', name: 'app_admin_seance_edit', requirements: ['id' => '\d+'])]
    public function seanceEdit(int $id): Response
    {
        $seance = ['id' => $id, 'cours_id' => 3, 'date' => '2025-04-05', 'heure' => '10:00', 'lieu' => 'Terrain principal', 'nb_places_max' => 15];
        $cours = [
            ['id' => 1, 'titre' => 'Éducation individuelle'],
            ['id' => 2, 'titre' => 'Cours chiot'],
            ['id' => 3, 'titre' => 'Obéissance débutant'],
            ['id' => 4, 'titre' => 'Obéissance confirmé'],
            ['id' => 5, 'titre' => 'Agility débutant'],
            ['id' => 6, 'titre' => 'Sociabilisation'],
        ];

        return $this->render('admin/seances/edit.html.twig', [
            'seance' => $seance,
            'cours'  => $cours,
        ]);
    }

    // ---- Inscriptions ----

    #[Route('/inscriptions', name: 'app_admin_inscriptions')]
    public function inscriptions(): Response
    {
        $inscriptions = [
            ['id' => 1, 'chien' => 'Rocky',   'proprietaire' => 'Dubois Marie',  'cours' => 'Obéissance débutant', 'date_seance' => '2025-04-05', 'heure' => '10:00', 'date_inscription' => '2025-03-10'],
            ['id' => 2, 'chien' => 'Luna',    'proprietaire' => 'Dubois Marie',  'cours' => 'Cours chiot',          'date_seance' => '2025-04-07', 'heure' => '14:00', 'date_inscription' => '2025-03-12'],
            ['id' => 3, 'chien' => 'Buddy',   'proprietaire' => 'Martin Jean',   'cours' => 'Agility débutant',    'date_seance' => '2025-04-12', 'heure' => '09:00', 'date_inscription' => '2025-03-14'],
            ['id' => 4, 'chien' => 'Bella',   'proprietaire' => 'Leroy Sophie',  'cours' => 'Sociabilisation',      'date_seance' => '2025-04-15', 'heure' => '15:00', 'date_inscription' => '2025-03-15'],
            ['id' => 5, 'chien' => 'Max',     'proprietaire' => 'Petit Paul',    'cours' => 'Obéissance débutant', 'date_seance' => '2025-04-05', 'heure' => '10:00', 'date_inscription' => '2025-03-11'],
        ];

        return $this->render('admin/inscriptions/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    // ---- Propriétaires ----

    #[Route('/proprietaires', name: 'app_admin_proprietaires')]
    public function proprietaires(ProprietaireRepository $proprietaireRepository): Response
    {

        $proprietaires = $proprietaireRepository->findAll();

        return $this->render('admin/proprietaires/index.html.twig', [
            'proprietaires' => $proprietaires,
        ]);
    }

    // Supression (calqué sur le tp2)
    #[Route('/admin/proprietaire/supprimer/{id}', name: 'admin_proprietaire_supprimer', methods: ['POST'])]
    public function supprimerProprietaire(
        Proprietaire $proprietaire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        if ($this->isCsrfTokenValid('delete' . $proprietaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($proprietaire);
            $entityManager->flush();
            $this->addFlash('success', 'Propriétaire supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_proprietaires');
    }

    // ---- Chiens ----

    #[Route('/chiens', name: 'app_admin_chiens')]
    public function chiens(ChienRepository $chienRepository): Response
    {
        $chiens = $chienRepository->findAll();

        return $this->render('admin/chiens/index.html.twig', [
            'chiens' => $chiens,
        ]);
    }

    #[Route('/admin/chien/supprimer/{id}', name: 'admin_chien_supprimer', methods: ['POST'])]
    public function supprimerChien(
        Chien $chien,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        if ($this->isCsrfTokenValid('delete' . $chien->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chien);
            $entityManager->flush();
            $this->addFlash('success', 'Chien supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_admin_chiens');
    }

}
