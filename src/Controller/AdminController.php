<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\Cours;
use App\Entity\Utilisateur;
use App\Entity\Proprietaire;
use App\Entity\Chien;
use App\Repository\InscriptionRepository;
use App\Repository\SeanceRepository;
use App\Repository\CoursRepository;
use App\Repository\ChienRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ProprietaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    private function getStatsFactices(): array
    {
        return [
            'nb_utilisateurs'  => 0,
            'nb_chiens'        => 0,
            'nb_cours'         => 0,
            'nb_seances'       => 0,
            'nb_inscriptions'  => 0,
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
        $utilisateurs = $utilisateurRepository->findAll();
        return $this->render('admin/utilisateurs/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    #[Route('/utilisateur/supprimer/{id}', name: 'admin_utilisateur_supprimer', methods: ['POST'])]
    public function supprimerUtilisateur(Utilisateur $utilisateur, Request $request, EntityManagerInterface $entityManager): Response 
    {
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
    public function cours(CoursRepository $coursRepository): Response
    {
        return $this->render('admin/cours/index.html.twig', [
            'cours' => $coursRepository->findAll()
        ]);
    }

    #[Route('/cours/nouveau', name: 'app_admin_cours_new', methods: ['GET', 'POST'])]
    public function coursNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cours = new Cours();
        if ($request->isMethod('POST')) {
            $cours->setTitre($request->request->get('titre'));
            $cours->setType($request->request->get('type'));
            $cours->setNiveau($request->request->get('niveau'));
            $cours->setDescription($request->request->get('description'));
            $cours->setPrix((float)$request->request->get('prix'));

            $entityManager->persist($cours);
            $entityManager->flush();

            $this->addFlash('success', 'Nouveau cours créé avec succès !');
            return $this->redirectToRoute('app_admin_cours');
        }
        return $this->render('admin/cours/new.html.twig');
    }

    #[Route('/cours/{id}/edit', name: 'app_admin_cours_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function coursEdit(Cours $cours, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $cours->setTitre($request->request->get('titre'));
            $cours->setType($request->request->get('type'));
            $cours->setNiveau($request->request->get('niveau'));
            $cours->setDescription($request->request->get('description'));
            $cours->setPrix((float)$request->request->get('prix'));

            $entityManager->flush();
            $this->addFlash('success', 'Le cours a été mis à jour.');
            return $this->redirectToRoute('app_admin_cours');
        }
        return $this->render('admin/cours/edit.html.twig', ['cours' => $cours]);
    }

    #[Route('/cours/supprimer/{id}', name: 'admin_cours_supprimer', methods: ['POST'])]
    public function supprimerCours(Cours $cours, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $cours->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cours);
            $entityManager->flush();
            $this->addFlash('success', 'Cours supprimé avec succès.');
        }
        return $this->redirectToRoute('app_admin_cours');
    }

    // ---- Séances ----

    #[Route('/seances', name: 'app_admin_seances')]
    public function seances(SeanceRepository $seanceRepository): Response
    {
        return $this->render('admin/seances/index.html.twig', [
            'seances' => $seanceRepository->findAll(),
        ]);
    }

    #[Route('/seances/nouvelle', name: 'app_admin_seance_new', methods: ['GET', 'POST'])]
    public function seanceNew(Request $request, EntityManagerInterface $entityManager, CoursRepository $coursRepository): Response 
    {
        $seance = new Seance();
        if ($request->isMethod('POST')) {
            $dateString = $request->request->get('date') . ' ' . $request->request->get('heure');
            $seance->setDateHeure(new \DateTime($dateString));
            $seance->setLieu($request->request->get('lieu'));
            $seance->setNbPlacesMax((int)$request->request->get('nb_places_max'));
            $seance->setCours($coursRepository->find($request->request->get('cours_id')));

            $entityManager->persist($seance);
            $entityManager->flush();
            $this->addFlash('success', 'Séance créée avec succès.');
            return $this->redirectToRoute('app_admin_seances');
        }
        return $this->render('admin/seances/new.html.twig', ['cours' => $coursRepository->findAll()]);
    }

    #[Route('/seances/supprimer/{id}', name: 'admin_seance_supprimer', methods: ['POST'])]
    public function supprimerSeance(Seance $seance, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $seance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
            $this->addFlash('success', 'La séance a été supprimée.');
        }
        return $this->redirectToRoute('app_admin_seances');
    }

    // ---- Inscriptions ----

    #[Route('/inscriptions', name: 'app_admin_inscriptions')]
    public function inscriptions(InscriptionRepository $inscriptionRepository): Response
    {
        // On récupère les vraies données de la BDD
        $inscriptions = $inscriptionRepository->findAll();

        return $this->render('admin/inscriptions/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    // ---- Propriétaires ----

    #[Route('/proprietaires', name: 'app_admin_proprietaires')]
    public function proprietaires(ProprietaireRepository $proprietaireRepository): Response
    {
        return $this->render('admin/proprietaires/index.html.twig', [
            'proprietaires' => $proprietaireRepository->findAll(),
        ]);
    }

    #[Route('/proprietaire/supprimer/{id}', name: 'admin_proprietaire_supprimer', methods: ['POST'])]
    public function supprimerProprietaire(Proprietaire $proprietaire, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $proprietaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($proprietaire);
            $entityManager->flush();
            $this->addFlash('success', 'Propriétaire supprimé avec succès.');
        }
        return $this->redirectToRoute('app_admin_proprietaires');
    }

    // ---- Chiens ----

    #[Route('/chiens', name: 'app_admin_chiens')]
    public function chiens(ChienRepository $chienRepository): Response
    {
        return $this->render('admin/chiens/index.html.twig', [
            'chiens' => $chienRepository->findAll(),
        ]);
    }

    #[Route('/chien/supprimer/{id}', name: 'admin_chien_supprimer', methods: ['POST'])]
    public function supprimerChien(Chien $chien, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $chien->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chien);
            $entityManager->flush();
            $this->addFlash('success', 'Chien supprimé avec succès.');
        }
        return $this->redirectToRoute('app_admin_chiens');
    }
} // Fin de la classe