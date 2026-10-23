<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Trajet
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                t.id,
                t.utilisateur_id,
                t.agence_depart_id,
                t.agence_arrivee_id,
                t.date_depart,
                t.heure_depart,
                t.date_arrivee,
                t.heure_arrivee,
                t.nombre_places_total,
                t.nombre_places_disponibles,
                a1.ville AS ville_depart,
                a2.ville AS ville_arrivee,
                u.prenom,
                u.nom
            FROM trajets t
            INNER JOIN agences a1 ON t.agence_depart_id = a1.id
            INNER JOIN agences a2 ON t.agence_arrivee_id = a2.id
            INNER JOIN utilisateurs u ON t.utilisateur_id = u.id
            WHERE TIMESTAMP(t.date_depart, t.heure_depart) > NOW()
              AND t.nombre_places_disponibles > 0
            ORDER BY t.date_depart ASC, t.heure_depart ASC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }
    public function getById(int $id): ?array
    {
        $sql = '
            SELECT
                t.id,
                t.utilisateur_id,
                t.agence_depart_id,
                t.agence_arrivee_id,
                t.date_depart,
                t.heure_depart,
                t.date_arrivee,
                t.heure_arrivee,
                t.nombre_places_total,
                t.nombre_places_disponibles,
                a1.ville AS ville_depart,
                a2.ville AS ville_arrivee,
                u.prenom,
                u.nom
            FROM trajets t
            INNER JOIN agences a1 ON t.agence_depart_id = a1.id
            INNER JOIN agences a2 ON t.agence_arrivee_id = a2.id
            INNER JOIN utilisateurs u ON t.utilisateur_id = u.id
            WHERE t.id = :id
            LIMIT 1
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $trajet = $stmt->fetch();

        return $trajet ?: null;
    }

    public function search(
        ?int $depart,
        ?int $arrivee,
        ?string $date
    ): array {
        $sql = "
            SELECT
                t.id,
                t.utilisateur_id,
                t.agence_depart_id,
                t.agence_arrivee_id,
                t.date_depart,
                t.heure_depart,
                t.date_arrivee,
                t.heure_arrivee,
                t.nombre_places_total,
                t.nombre_places_disponibles,
                a1.ville AS ville_depart,
                a2.ville AS ville_arrivee,
                u.prenom,
                u.nom
            FROM trajets t
            INNER JOIN agences a1 ON t.agence_depart_id = a1.id
            INNER JOIN agences a2 ON t.agence_arrivee_id = a2.id
            INNER JOIN utilisateurs u ON t.utilisateur_id = u.id
            WHERE TIMESTAMP(t.date_depart, t.heure_depart) > NOW()
              AND t.nombre_places_disponibles > 0
        ";

        $params = [];

        if ($depart !== null) {
            $sql .= " AND t.agence_depart_id = :depart";
            $params['depart'] = $depart;
        }

        if ($arrivee !== null) {
            $sql .= " AND t.agence_arrivee_id = :arrivee";
            $params['arrivee'] = $arrivee;
        }

        if ($date !== null && $date !== '') {
            $sql .= " AND t.date_depart = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY t.date_depart ASC, t.heure_depart ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
    public function creer(
        int $utilisateurId,
        int $agenceDepartId,
        int $agenceArriveeId,
        string $dateDepart,
        string $heureDepart,
        string $dateArrivee,
        string $heureArrivee,
        int $nombrePlaces
    ): int {
        if (
            $utilisateurId <= 0 ||
            $agenceDepartId <= 0 ||
            $agenceArriveeId <= 0 ||
            $agenceDepartId === $agenceArriveeId ||
            $nombrePlaces < 1 ||
            $nombrePlaces > 255
        ) {
            throw new \InvalidArgumentException('Les informations du trajet sont invalides.');
        }

        $depart = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateDepart);
        $arrivee = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateArrivee);
        $heureDep = \DateTimeImmutable::createFromFormat('!H:i', $heureDepart);
        $heureArr = \DateTimeImmutable::createFromFormat('!H:i', $heureArrivee);

        if (
            !$depart || $depart->format('Y-m-d') !== $dateDepart ||
            !$arrivee || $arrivee->format('Y-m-d') !== $dateArrivee ||
            !$heureDep || $heureDep->format('H:i') !== $heureDepart ||
            !$heureArr || $heureArr->format('H:i') !== $heureArrivee
        ) {
            throw new \InvalidArgumentException('La date ou l’heure est invalide.');
        }

        $departComplet = \DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i',
            $dateDepart . ' ' . $heureDepart
        );
        $arriveeComplete = \DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i',
            $dateArrivee . ' ' . $heureArrivee
        );

        if ($departComplet <= new \DateTimeImmutable('now') ||
            $arriveeComplete <= $departComplet) {
            throw new \InvalidArgumentException('Les dates doivent être futures et l’arrivée après le départ.');
        }

        $sql = 'INSERT INTO trajets (
                    utilisateur_id,
                    agence_depart_id,
                    agence_arrivee_id,
                    date_depart,
                    heure_depart,
                    date_arrivee,
                    heure_arrivee,
                    nombre_places_total,
                    nombre_places_disponibles
                ) VALUES (
                    :utilisateur_id,
                    :depart_id,
                    :arrivee_id,
                    :date_depart,
                    :heure_depart,
                    :date_arrivee,
                    :heure_arrivee,
                    :places,
                    :places_disponibles
                )';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'utilisateur_id' => $utilisateurId,
            'depart_id' => $agenceDepartId,
            'arrivee_id' => $agenceArriveeId,
            'date_depart' => $dateDepart,
            'heure_depart' => $heureDepart . ':00',
            'date_arrivee' => $dateArrivee,
            'heure_arrivee' => $heureArrivee . ':00',
            'places' => $nombrePlaces,
            'places_disponibles' => $nombrePlaces,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function supprimer(int $trajetId, int $utilisateurId): bool
    {
        $verification = $this->pdo->prepare(
            'SELECT id FROM trajets
             WHERE id = :trajet_id AND utilisateur_id = :utilisateur_id'
        );
        $verification->execute([
            'trajet_id' => $trajetId,
            'utilisateur_id' => $utilisateurId,
        ]);

        if (!$verification->fetch()) {
            return false;
        }

        $reservations = $this->pdo->prepare(
            'SELECT COUNT(*) FROM reservations WHERE trajet_id = :trajet_id'
        );
        $reservations->execute(['trajet_id' => $trajetId]);

        if ((int) $reservations->fetchColumn() > 0) {
            throw new \RuntimeException(
                'Ce trajet possède des réservations et ne peut pas être supprimé.'
            );
        }

        $suppression = $this->pdo->prepare(
            'DELETE FROM trajets
             WHERE id = :trajet_id AND utilisateur_id = :utilisateur_id'
        );

        $suppression->execute([
            'trajet_id' => $trajetId,
            'utilisateur_id' => $utilisateurId,
        ]);

        return $suppression->rowCount() === 1;
    }

    public function modifier(
        int $trajetId,
        int $utilisateurId,
        int $agenceDepartId,
        int $agenceArriveeId,
        string $dateDepart,
        string $heureDepart,
        string $dateArrivee,
        string $heureArrivee,
        int $nombrePlaces
    ): bool {
        if (
            $trajetId <= 0 ||
            $utilisateurId <= 0 ||
            $agenceDepartId <= 0 ||
            $agenceArriveeId <= 0 ||
            $agenceDepartId === $agenceArriveeId ||
            $nombrePlaces < 1 ||
            $nombrePlaces > 255
        ) {
            throw new \InvalidArgumentException(
                'Les informations du trajet sont invalides.'
            );
        }

        $depart = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateDepart);
        $arrivee = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateArrivee);
        $heureDep = \DateTimeImmutable::createFromFormat('!H:i', $heureDepart);
        $heureArr = \DateTimeImmutable::createFromFormat('!H:i', $heureArrivee);

        if (
            !$depart || $depart->format('Y-m-d') !== $dateDepart ||
            !$arrivee || $arrivee->format('Y-m-d') !== $dateArrivee ||
            !$heureDep || $heureDep->format('H:i') !== $heureDepart ||
            !$heureArr || $heureArr->format('H:i') !== $heureArrivee
        ) {
            throw new \InvalidArgumentException(
                'La date ou l’heure est invalide.'
            );
        }

        $departComplet = \DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i',
            $dateDepart . ' ' . $heureDepart
        );
        $arriveeComplete = \DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i',
            $dateArrivee . ' ' . $heureArrivee
        );

        if (
            !$departComplet ||
            !$arriveeComplete ||
            $departComplet <= new \DateTimeImmutable('now') ||
            $arriveeComplete <= $departComplet
        ) {
            throw new \InvalidArgumentException(
                'Le départ doit être futur et l’arrivée après le départ.'
            );
        }

        $verification = $this->pdo->prepare(
            'SELECT nombre_places_total, nombre_places_disponibles
             FROM trajets
             WHERE id = :id AND utilisateur_id = :utilisateur_id'
        );
        $verification->execute([
            'id' => $trajetId,
            'utilisateur_id' => $utilisateurId,
        ]);

        $trajetExistant = $verification->fetch(PDO::FETCH_ASSOC);

        if (!$trajetExistant) {
            return false;
        }

        $placesReservees =
            (int) $trajetExistant['nombre_places_total'] -
            (int) $trajetExistant['nombre_places_disponibles'];

        if ($nombrePlaces < $placesReservees) {
            throw new \InvalidArgumentException(
                'Le nombre de places ne peut pas être inférieur aux places déjà réservées.'
            );
        }

        $placesDisponibles = $nombrePlaces - $placesReservees;

        $requete = $this->pdo->prepare(
            'UPDATE trajets
             SET agence_depart_id = :depart_id,
                 agence_arrivee_id = :arrivee_id,
                 date_depart = :date_depart,
                 heure_depart = :heure_depart,
                 date_arrivee = :date_arrivee,
                 heure_arrivee = :heure_arrivee,
                 nombre_places_total = :places_total,
                 nombre_places_disponibles = :places_disponibles
             WHERE id = :id AND utilisateur_id = :utilisateur_id'
        );

        $requete->execute([
            'depart_id' => $agenceDepartId,
            'arrivee_id' => $agenceArriveeId,
            'date_depart' => $dateDepart,
            'heure_depart' => $heureDepart . ':00',
            'date_arrivee' => $dateArrivee,
            'heure_arrivee' => $heureArrivee . ':00',
            'places_total' => $nombrePlaces,
            'places_disponibles' => $placesDisponibles,
            'id' => $trajetId,
            'utilisateur_id' => $utilisateurId,
        ]);

        return true;
    }
}