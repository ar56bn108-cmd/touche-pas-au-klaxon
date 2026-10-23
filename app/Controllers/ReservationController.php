<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Reservation;
use PDO;
use RuntimeException;
use Throwable;

class ReservationController
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function creer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée.';
            return;
        }

        // Vérifier que l'utilisateur est connecté.
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

        $utilisateurId = (int) $utilisateur['id'];

        $trajetId = filter_input(
            INPUT_POST,
            'trajet_id',
            FILTER_VALIDATE_INT
        );

        $nombrePlaces = filter_input(
            INPUT_POST,
            'nombre_places',
            FILTER_VALIDATE_INT
        );

        if (
            $trajetId === false || $trajetId === null || $trajetId <= 0 ||
            $nombrePlaces === false || $nombrePlaces === null ||
            $nombrePlaces <= 0 || $nombrePlaces > 127
        ) {
            http_response_code(400);
            echo 'Les informations de réservation sont invalides.';
            return;
        }

        try {
            $reservation = new Reservation($this->pdo);

            $reservation->creer(
                $trajetId,
                $utilisateurId,
                $nombrePlaces
            );

            header(
                'Location: /trajet?id=' . $trajetId . '&reservation=success'
            );
            exit;
        } catch (RuntimeException $erreur) {
            http_response_code(400);
            echo htmlspecialchars(
                $erreur->getMessage(),
                ENT_QUOTES,
                'UTF-8'
            );
        } catch (Throwable $erreur) {
            error_log($erreur->getMessage());
            http_response_code(500);
            echo 'Une erreur est survenue pendant la réservation.';
        }
    }


    public function mesReservations(): void
    {
        $utilisateur = $_SESSION['utilisateur'] ?? null;

        if (!is_array($utilisateur) || !isset($utilisateur['id'])
            || !is_numeric($utilisateur['id']) || (int) $utilisateur['id'] <= 0) {
            header('Location: /connexion');
            exit;
        }

        $modele = new Reservation($this->pdo);
        $reservations = $modele->getPourUtilisateur((int) $utilisateur['id']);
        require __DIR__ . '/../Views/mes-reservations.php';
    }

    public function annuler(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée.';
            return;
        }

        $utilisateur = $_SESSION['utilisateur'] ?? null;

        if (!is_array($utilisateur) || !isset($utilisateur['id'])
            || !is_numeric($utilisateur['id']) || (int) $utilisateur['id'] <= 0) {
            header('Location: /connexion');
            exit;
        }

        $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);

        if ($reservationId === false || $reservationId === null || $reservationId <= 0) {
            header('Location: /mes-reservations?erreur=identifiant');
            exit;
        }

        try {
            $modele = new Reservation($this->pdo);
            $modele->annuler((int) $reservationId, (int) $utilisateur['id']);
            header('Location: /mes-reservations?annulation=success');
            exit;
        } catch (RuntimeException $erreur) {
            header('Location: /mes-reservations?erreur=annulation');
            exit;
        } catch (Throwable $erreur) {
            error_log($erreur->getMessage());
            http_response_code(500);
            echo "Une erreur est survenue pendant l'annulation.";
        }
    }
}