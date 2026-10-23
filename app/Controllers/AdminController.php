<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;

class AdminController
{
    public function __construct(private PDO $pdo)
    {
    }

    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $utilisateur = $_SESSION['utilisateur'] ?? null;

        if (
            !is_array($utilisateur)
            || ($utilisateur['role'] ?? '') !== 'admin'
        ) {
            http_response_code(403);
            echo 'Accès interdit. Cette page est réservée aux administrateurs.';
            return;
        }

        $requeteUtilisateurs = $this->pdo->query(
            'SELECT id, nom, prenom, email, telephone, role
             FROM utilisateurs
             ORDER BY id DESC'
        );
        $utilisateurs = $requeteUtilisateurs->fetchAll(PDO::FETCH_ASSOC);

        $requeteTrajets = $this->pdo->query(
            'SELECT
                t.id,
                t.date_depart,
                t.heure_depart,
                t.nombre_places_disponibles,
                t.nombre_places_total,
                u.nom,
                u.prenom,
                ad.ville AS ville_depart,
                aa.ville AS ville_arrivee
             FROM trajets t
             INNER JOIN utilisateurs u ON u.id = t.utilisateur_id
             INNER JOIN agences ad ON ad.id = t.agence_depart_id
             INNER JOIN agences aa ON aa.id = t.agence_arrivee_id
             ORDER BY t.date_depart DESC, t.heure_depart DESC'
        );
        $trajets = $requeteTrajets->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/admin.php';
    }
}