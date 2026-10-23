<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Trajet;
use App\Models\Agence;
use InvalidArgumentException;
use RuntimeException;

class ModifierTrajetController
{
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['utilisateur']['id'])) {
            header('Location: /connexion');
            exit;
        }

        $pdo = require dirname(__DIR__, 2) . '/config/database.php';

        $trajetModel = new Trajet($pdo);
        $agenceModel = new Agence($pdo);

        $trajetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$trajetId || $trajetId <= 0) {
            http_response_code(400);
            echo 'Identifiant de trajet invalide.';
            return;
        }

        $utilisateurId = (int) $_SESSION['utilisateur']['id'];
        $trajet = $trajetModel->getById($trajetId);

        if (!$trajet || (int) $trajet['utilisateur_id'] !== $utilisateurId) {
            http_response_code(404);
            echo 'Trajet introuvable ou accès non autorisé.';
            return;
        }

        $agences = $agenceModel->getAll();
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $agenceDepartId = filter_input(
                    INPUT_POST,
                    'agence_depart_id',
                    FILTER_VALIDATE_INT
                );
                $agenceArriveeId = filter_input(
                    INPUT_POST,
                    'agence_arrivee_id',
                    FILTER_VALIDATE_INT
                );
                $nombrePlaces = filter_input(
                    INPUT_POST,
                    'nombre_places_total',
                    FILTER_VALIDATE_INT
                );

                $dateDepart = trim((string) ($_POST['date_depart'] ?? ''));
                $heureDepart = trim((string) ($_POST['heure_depart'] ?? ''));
                $dateArrivee = trim((string) ($_POST['date_arrivee'] ?? ''));
                $heureArrivee = trim((string) ($_POST['heure_arrivee'] ?? ''));

                if (
                    !$agenceDepartId ||
                    !$agenceArriveeId ||
                    !$nombrePlaces
                ) {
                    throw new InvalidArgumentException(
                        'Veuillez renseigner tous les champs correctement.'
                    );
                }

                $modifie = $trajetModel->modifier(
                    $trajetId,
                    $utilisateurId,
                    $agenceDepartId,
                    $agenceArriveeId,
                    $dateDepart,
                    $heureDepart,
                    $dateArrivee,
                    $heureArrivee,
                    $nombrePlaces
                );

                if (!$modifie) {
                    throw new RuntimeException(
                        'Le trajet n’a pas pu être modifié.'
                    );
                }

                $_SESSION['message_succes'] = 'Trajet modifié avec succès.';
                header('Location: /mes-trajets');
                exit;
            } catch (InvalidArgumentException | RuntimeException $exception) {
                $erreur = $exception->getMessage();
            }
        }

        require dirname(__DIR__) . '/Views/modifier-trajet.php';
    }
}