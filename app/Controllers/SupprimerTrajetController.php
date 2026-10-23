<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Trajet;
use PDO;
use RuntimeException;
use Throwable;

class SupprimerTrajetController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function supprimer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée.';
            return;
        }

        $utilisateur = $_SESSION['utilisateur'] ?? null;

        if (
            !is_array($utilisateur) ||
            !isset($utilisateur['id']) ||
            !is_numeric($utilisateur['id']) ||
            (int) $utilisateur['id'] <= 0
        ) {
            header('Location: /connexion');
            exit;
        }

        $trajetId = filter_input(INPUT_POST, 'trajet_id', FILTER_VALIDATE_INT);

        if (!$trajetId || $trajetId <= 0) {
            header('Location: /mes-trajets?erreur=id');
            exit;
        }

        try {
            $trajet = new Trajet($this->pdo);
            $supprime = $trajet->supprimer(
                (int) $trajetId,
                (int) $utilisateur['id']
            );

            header(
                'Location: /mes-trajets?' .
                ($supprime ? 'suppression=success' : 'erreur=suppression')
            );
            exit;
        } catch (RuntimeException $e) {
            header('Location: /mes-trajets?erreur=reservations');
            exit;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            header('Location: /mes-trajets?erreur=suppression');
            exit;
        }
    }
}