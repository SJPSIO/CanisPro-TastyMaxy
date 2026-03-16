<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CoursController extends AbstractController
{
    // Données fictives pour la démo graphique
    private function getCoursFactices(): array
    {
        return [
            ['id' => 1, 'titre' => 'Éducation individuelle', 'type' => 'Individuel', 'niveau' => 'Tous niveaux', 'description' => 'Séance personnalisée en tête-à-tête avec l\'éducateur. Programme adapté aux besoins spécifiques de votre chien, de l\'obéissance de base aux comportements avancés.', 'prix' => 65.00, 'icone' => 'fa-user'],
            ['id' => 2, 'titre' => 'Cours chiot',           'type' => 'Collectif',  'niveau' => 'Chiot',       'description' => 'Socialisation et bases de l\'éducation pour les chiots de 2 à 6 mois. Apprentissage des commandes fondamentales dans une ambiance ludique et bienveillante.', 'prix' => 40.00, 'icone' => 'fa-dog'],
            ['id' => 3, 'titre' => 'Obéissance débutant',   'type' => 'Collectif',  'niveau' => 'Débutant',    'description' => 'Cours collectif pour les duos maître-chien commençant leur parcours éducatif. Au programme : assis, couché, rappel, marche en laisse.', 'prix' => 35.00, 'icone' => 'fa-graduation-cap'],
            ['id' => 4, 'titre' => 'Obéissance confirmé',   'type' => 'Collectif',  'niveau' => 'Confirmé',    'description' => 'Pour les chiens maîtrisant les bases. Exercices avancés, distances, distractions et perfectionnement des acquis.', 'prix' => 38.00, 'icone' => 'fa-trophy'],
            ['id' => 5, 'titre' => 'Agility débutant',      'type' => 'Collectif',  'niveau' => 'Débutant',    'description' => 'Initiation aux parcours d\'agility : slalom, tunnels, sauts, passerelle. Une activité physique et mentale stimulante pour le chien et son maître.', 'prix' => 42.00, 'icone' => 'fa-person-running'],
            ['id' => 6, 'titre' => 'Sociabilisation',       'type' => 'Collectif',  'niveau' => 'Chiot',       'description' => 'Séances dédiées à la socialisation entre chiens et avec l\'environnement : bruits, enfants, vélos, autres animaux.', 'prix' => 32.00, 'icone' => 'fa-heart'],
        ];
    }

    #[Route('/cours', name: 'app_cours_index')]
    public function index(): Response
    {
        return $this->render('cours/index.html.twig', [
            'cours' => $this->getCoursFactices(),
        ]);
    }

    #[Route('/cours/{id}', name: 'app_cours_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        // Trouver le cours dans les données fictives
        $cours = null;
        foreach ($this->getCoursFactices() as $c) {
            if ($c['id'] === $id) {
                $cours = $c;
                break;
            }
        }

        if (!$cours) {
            throw $this->createNotFoundException('Cours introuvable.');
        }

        // Séances fictives pour ce cours
        $seances = [
            ['id' => 1, 'date' => '2025-04-05', 'heure' => '10:00', 'lieu' => 'Terrain principal', 'places_restantes' => 8],
            ['id' => 2, 'date' => '2025-04-12', 'heure' => '14:00', 'lieu' => 'Terrain principal', 'places_restantes' => 3],
            ['id' => 3, 'date' => '2025-04-19', 'heure' => '10:00', 'lieu' => 'Terrain A', 'places_restantes' => 12],
            ['id' => 4, 'date' => '2025-04-26', 'heure' => '16:00', 'lieu' => 'Terrain principal', 'places_restantes' => 0],
        ];

        return $this->render('cours/show.html.twig', [
            'cours'   => $cours,
            'seances' => $seances,
        ]);
    }
}
