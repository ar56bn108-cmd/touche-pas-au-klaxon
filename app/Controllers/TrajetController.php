<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Trajet;
use PDO;

class TrajetController
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function index(): void
    {
        $trajetModel = new Trajet($this->pdo);

        $depart = filter_input(INPUT_GET, 'depart', FILTER_VALIDATE_INT);
        $arrivee = filter_input(INPUT_GET, 'arrivee', FILTER_VALIDATE_INT);
        $date = $_GET['date'] ?? '';

        if (!is_string($date)) {
            $date = '';
        }

        if (
            $date !== ''
            && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
        ) {
            $date = '';
        }

        $agenceDepartId = (
            $depart !== false && $depart !== null && $depart > 0
        ) ? $depart : null;

        $agenceArriveeId = (
            $arrivee !== false && $arrivee !== null && $arrivee > 0
        ) ? $arrivee : null;

        if (
            $agenceDepartId !== null
            || $agenceArriveeId !== null
            || $date !== ''
        ) {
            $trajets = $trajetModel->search(
                $agenceDepartId,
                $agenceArriveeId,
                $date !== '' ? $date : null
            );
        } else {
            $trajets = $trajetModel->getAll();
        }

        require __DIR__ . '/../Views/trajets.php';
    }
}