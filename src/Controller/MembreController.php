<?php

namespace App\Controller;

use App\Entity\Chien;
use App\Entity\Inscription;
use App\Form\ChienType;
use App\Repository\ChienRepository;
use App\Repository\InscriptionRepository;
use App\Repository\ProprietaireRepository;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


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
        $user = $this->getUser();
        $proprietaire = $user->getProprietaire();
        $chiens = $chienRepository->findBy(['proprietaire' => $proprietaire]);

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

            $user = $this->getUser();
            $proprietaire = $user->getProprietaire();
            $chien->setProprietaire($proprietaire);

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
            'chien' => $chien,
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
    public function seances(
        SeanceRepository $seanceRepository,
        ChienRepository $chienRepository
    ): Response {
        $seances = $seanceRepository->findAll();
        $chiens = $chienRepository->findAll();

        return $this->render('membre/seances.html.twig', [
            'seances' => $seances,
            'chiens' => $chiens,
        ]);
    }

    #[Route('/seances/{id}/inscrire', name: 'app_membre_seance_inscrire', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function inscrireChien(
        int $id,
        Request $request,
        SeanceRepository $seanceRepository,
        ChienRepository $chienRepository,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $seance = $seanceRepository->find($id);

        if (!$seance) {
            throw $this->createNotFoundException('Séance introuvable');
        }

        $chienId = $request->request->get('chien_id');

        if (!$chienId) {
            $this->addFlash('error', 'Veuillez choisir un chien.');
            return $this->redirectToRoute('app_membre_seances');
        }

        $chien = $chienRepository->find($chienId);

        if (!$chien) {
            $this->addFlash('error', 'Chien introuvable.');
            return $this->redirectToRoute('app_membre_seances');
        }

        if ($seance->estComplete()) {
            $this->addFlash('error', 'Cette séance est complète.');
            return $this->redirectToRoute('app_membre_seances');
        }

        $inscriptionsExistantes = $inscriptionRepository->findAll();

        foreach ($inscriptionsExistantes as $inscriptionExistante) {
            if (
                $inscriptionExistante->getChien()?->getId() === $chien->getId()
                && $inscriptionExistante->getSeance()?->getId() === $seance->getId()
            ) {
                $this->addFlash('error', 'Ce chien est déjà inscrit à cette séance.');
                return $this->redirectToRoute('app_membre_seances');
            }
        }

        $inscription = new Inscription();
        $inscription->setChien($chien);
        $inscription->setSeance($seance);
        $inscription->setDateInscription(new \DateTime());

        $entityManager->persist($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'Le chien a bien été inscrit à la séance.');

        return $this->redirectToRoute('app_membre_inscriptions');
    }

    #[Route('/inscriptions', name: 'app_membre_inscriptions')]
    public function inscriptions(InscriptionRepository $inscriptionRepository): Response
    {
        $inscriptions = $inscriptionRepository->findAll();

        return $this->render('membre/inscriptions.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }

    #[Route('/inscriptions/{id}/supprimer', name: 'app_membre_inscription_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprimerInscription(
        int $id,
        InscriptionRepository $inscriptionRepository,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $inscription = $inscriptionRepository->find($id);

        if (!$inscription) {
            throw $this->createNotFoundException('Inscription introuvable');
        }

        $entityManager->remove($inscription);
        $entityManager->flush();

        $this->addFlash('success', 'L’inscription a bien été annulée.');

        return $this->redirectToRoute('app_membre_inscriptions');
    }
}