<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Reservation;
use App\Models\Trajet;
use PDO;

class TrajetDetailController
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function show(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id <= 0) {
            http_response_code(400);
            echo 'Identifiant de trajet invalide.';
            return;
        }

        $trajetModel = new Trajet($this->pdo);
        $trajet = $trajetModel->getById($id);

        if ($trajet === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $utilisateur = $_SESSION['utilisateur'] ?? null;
        $utilisateurId = is_array($utilisateur) && isset($utilisateur['id'])
            ? (int) $utilisateur['id']
            : 0;

        $estConducteur = $utilisateurId > 0
            && $utilisateurId === (int) $trajet['utilisateur_id'];

        $aDejaReserve = false;

        if ($utilisateurId > 0 && !$estConducteur) {
            $reservationModel = new Reservation($this->pdo);
            $aDejaReserve = $reservationModel->existePourUtilisateur(
                $id,
                $utilisateurId
            );
        }

        require __DIR__ . '/../Views/trajet-detail.php';
    }
}