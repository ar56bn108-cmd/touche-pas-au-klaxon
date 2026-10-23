<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use RuntimeException;
use Throwable;

class Reservation
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function existePourUtilisateur(int $trajetId, int $utilisateurId): bool
    {
        $requete = $this->pdo->prepare(
            'SELECT id
             FROM reservations
             WHERE trajet_id = :trajet_id
               AND utilisateur_id = :utilisateur_id
             LIMIT 1'
        );

        $requete->execute([
            'trajet_id' => $trajetId,
            'utilisateur_id' => $utilisateurId,
        ]);

        return $requete->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function creer(
        int $trajetId,
        int $utilisateurId,
        int $nombrePlaces = 1
    ): bool {
        if ($trajetId <= 0 || $utilisateurId <= 0 || $nombrePlaces <= 0) {
            throw new RuntimeException('Les informations de réservation sont invalides.');
        }

        try {
            $this->pdo->beginTransaction();

            $requete = $this->pdo->prepare(
                'SELECT utilisateur_id, nombre_places_disponibles
                 FROM trajets
                 WHERE id = :trajet_id
                 FOR UPDATE'
            );

            $requete->execute([
                'trajet_id' => $trajetId,
            ]);

            $trajet = $requete->fetch(PDO::FETCH_ASSOC);

            if (!$trajet) {
                throw new RuntimeException('Ce trajet n’existe pas.');
            }

            if ((int) $trajet['utilisateur_id'] === $utilisateurId) {
                throw new RuntimeException('Tu ne peux pas réserver ton propre trajet.');
            }

            if ((int) $trajet['nombre_places_disponibles'] < $nombrePlaces) {
                throw new RuntimeException('Il n’y a pas assez de places disponibles.');
            }

            $requete = $this->pdo->prepare(
                'SELECT id
                 FROM reservations
                 WHERE trajet_id = :trajet_id
                   AND utilisateur_id = :utilisateur_id
                 LIMIT 1'
            );

            $requete->execute([
                'trajet_id' => $trajetId,
                'utilisateur_id' => $utilisateurId,
            ]);

            if ($requete->fetch(PDO::FETCH_ASSOC)) {
                throw new RuntimeException('Tu as déjà réservé ce trajet.');
            }

            $requete = $this->pdo->prepare(
                'INSERT INTO reservations
                    (trajet_id, utilisateur_id, nombre_places)
                 VALUES
                    (:trajet_id, :utilisateur_id, :nombre_places)'
            );

            $requete->execute([
                'trajet_id' => $trajetId,
                'utilisateur_id' => $utilisateurId,
                'nombre_places' => $nombrePlaces,
            ]);

            $requete = $this->pdo->prepare(
                'UPDATE trajets
                 SET nombre_places_disponibles = nombre_places_disponibles - :nombre_places
                 WHERE id = :trajet_id'
            );

            $requete->execute([
                'nombre_places' => $nombrePlaces,
                'trajet_id' => $trajetId,
            ]);

            $this->pdo->commit();

            return true;
        } catch (Throwable $erreur) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $erreur;
        }
    }

    public function getPourUtilisateur(int $utilisateurId): array
    {
        $sql = "
            SELECT r.id AS reservation_id, r.nombre_places, r.date_reservation,
                   t.id AS trajet_id, t.date_depart, t.heure_depart,
                   t.date_arrivee, t.heure_arrivee,
                   t.nombre_places_disponibles,
                   a1.ville AS ville_depart, a2.ville AS ville_arrivee,
                   u.prenom AS conducteur_prenom, u.nom AS conducteur_nom
            FROM reservations r
            INNER JOIN trajets t ON t.id = r.trajet_id
            INNER JOIN agences a1 ON a1.id = t.agence_depart_id
            INNER JOIN agences a2 ON a2.id = t.agence_arrivee_id
            INNER JOIN utilisateurs u ON u.id = t.utilisateur_id
            WHERE r.utilisateur_id = :utilisateur_id
            ORDER BY t.date_depart ASC, t.heure_depart ASC
        ";
        $requete = $this->pdo->prepare($sql);
        $requete->execute(['utilisateur_id' => $utilisateurId]);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public function annuler(int $reservationId, int $utilisateurId): bool
    {
        if ($reservationId <= 0 || $utilisateurId <= 0) {
            throw new RuntimeException('Réservation invalide.');
        }

        try {
            $this->pdo->beginTransaction();

            $requete = $this->pdo->prepare(
                'SELECT trajet_id, nombre_places
                 FROM reservations
                 WHERE id = :id AND utilisateur_id = :utilisateur_id
                 FOR UPDATE'
            );
            $requete->execute([
                'id' => $reservationId,
                'utilisateur_id' => $utilisateurId,
            ]);
            $reservation = $requete->fetch(PDO::FETCH_ASSOC);

            if (!$reservation) {
                throw new RuntimeException('Réservation introuvable ou non autorisée.');
            }

            $requete = $this->pdo->prepare(
                'SELECT id FROM trajets WHERE id = :id FOR UPDATE'
            );
            $requete->execute(['id' => (int) $reservation['trajet_id']]);

            if (!$requete->fetch(PDO::FETCH_ASSOC)) {
                throw new RuntimeException('Trajet introuvable.');
            }

            $requete = $this->pdo->prepare(
                'UPDATE trajets
                 SET nombre_places_disponibles =
                     LEAST(nombre_places_total,
                           nombre_places_disponibles + :places)
                 WHERE id = :trajet_id'
            );
            $requete->execute([
                'places' => (int) $reservation['nombre_places'],
                'trajet_id' => (int) $reservation['trajet_id'],
            ]);

            $requete = $this->pdo->prepare(
                'DELETE FROM reservations
                 WHERE id = :id AND utilisateur_id = :utilisateur_id'
            );
            $requete->execute([
                'id' => $reservationId,
                'utilisateur_id' => $utilisateurId,
            ]);

            if ($requete->rowCount() !== 1) {
                throw new RuntimeException('La réservation n’a pas pu être annulée.');
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $erreur) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $erreur;
        }
    }
}
