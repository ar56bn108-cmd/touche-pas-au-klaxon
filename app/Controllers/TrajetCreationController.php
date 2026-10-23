<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agence;
use App\Models\Trajet;
use InvalidArgumentException;
use PDO;
use Throwable;

class TrajetCreationController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function creer(): void
    {
        if (empty($_SESSION['utilisateur']['id'])) {
            header('Location: /connexion');
            exit;
        }

        $agences = (new Agence($this->pdo))->getAll();
        $erreur = '';
        $departId = 0;
        $arriveeId = 0;
        $dateDepart = '';
        $heureDepart = '';
        $dateArrivee = '';
        $heureArrivee = '';
        $nombrePlaces = 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $departId = filter_input(INPUT_POST, 'agence_depart_id', FILTER_VALIDATE_INT) ?: 0;
            $arriveeId = filter_input(INPUT_POST, 'agence_arrivee_id', FILTER_VALIDATE_INT) ?: 0;
            $dateDepart = trim((string) ($_POST['date_depart'] ?? ''));
            $heureDepart = trim((string) ($_POST['heure_depart'] ?? ''));
            $dateArrivee = trim((string) ($_POST['date_arrivee'] ?? ''));
            $heureArrivee = trim((string) ($_POST['heure_arrivee'] ?? ''));
            $nombrePlaces = filter_input(INPUT_POST, 'nombre_places', FILTER_VALIDATE_INT) ?: 0;

            $idsAgences = array_map(
                static fn(array $agence): int => (int) $agence['id'],
                $agences
            );

            if (
                !in_array($departId, $idsAgences, true) ||
                !in_array($arriveeId, $idsAgences, true)
            ) {
                $erreur = 'Choisis deux agences valides.';
            } else {
                try {
                    $trajet = new Trajet($this->pdo);
                    $id = $trajet->creer(
                        (int) $_SESSION['utilisateur']['id'],
                        $departId,
                        $arriveeId,
                        $dateDepart,
                        $heureDepart,
                        $dateArrivee,
                        $heureArrivee,
                        $nombrePlaces
                    );

                    header('Location: /trajet?id=' . $id . '&creation=success');
                    exit;
                } catch (InvalidArgumentException $e) {
                    $erreur = $e->getMessage();
                } catch (Throwable $e) {
                    error_log((string) $e);
                    $erreur = 'Impossible de créer le trajet pour le moment.';
                }
            }
        }

        require __DIR__ . '/../Views/trajet-creation.php';
    }
}