<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;

class MesTrajetsController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function index(): void
    {
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

        $sql = "
            SELECT
                t.id,
                t.date_depart,
                t.heure_depart,
                t.date_arrivee,
                t.heure_arrivee,
                t.nombre_places_total,
                t.nombre_places_disponibles,
                a1.ville AS ville_depart,
                a2.ville AS ville_arrivee
            FROM trajets t
            INNER JOIN agences a1 ON a1.id = t.agence_depart_id
            INNER JOIN agences a2 ON a2.id = t.agence_arrivee_id
            WHERE t.utilisateur_id = :utilisateur_id
            ORDER BY t.date_depart DESC, t.heure_depart DESC
        ";


        $requete = $this->pdo->prepare($sql);
        $requete->execute([
            'utilisateur_id' => (int) $utilisateur['id'],
        ]);

        $trajets = $requete->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/mes-trajets.php';
    }
}