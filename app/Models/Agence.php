<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use InvalidArgumentException;
use PDOException;

class Agence
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getAll(): array
    {
        $sql = 'SELECT id, ville FROM agences ORDER BY ville ASC';
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creer(string $ville): bool
    {
        $ville = trim($ville);

        if ($ville === '' || mb_strlen($ville) > 100) {
            throw new InvalidArgumentException(
                'Le nom de la ville doit contenir entre 1 et 100 caractères.'
            );
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO agences (ville) VALUES (:ville)'
        );

        try {
            return $stmt->execute(['ville' => $ville]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException(
                    'Cette ville existe déjà dans les agences.',
                    0,
                    $e
                );
            }
            throw $e;
        }
    }

    public function modifier(int $id, string $ville): bool
    {
        $ville = trim($ville);

        if ($id <= 0 || $ville === '' || mb_strlen($ville) > 100) {
            throw new InvalidArgumentException('Identifiant ou nom de ville invalide.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE agences SET ville = :ville WHERE id = :id'
        );

        try {
            $stmt->execute(['ville' => $ville, 'id' => $id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException(
                    'Cette ville existe déjà dans les agences.',
                    0,
                    $e
                );
            }
            throw $e;
        }

        return $stmt->rowCount() > 0;
    }

    public function supprimer(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Identifiant d’agence invalide.');
        }

        $verifier = $this->pdo->prepare(
            'SELECT COUNT(*) FROM trajets
             WHERE agence_depart_id = :depart OR agence_arrivee_id = :arrivee'
        );
        $verifier->execute(['depart' => $id, 'arrivee' => $id]);

        if ((int) $verifier->fetchColumn() > 0) {
            throw new InvalidArgumentException(
                'Cette agence est utilisée par un ou plusieurs trajets et ne peut pas être supprimée.'
            );
        }

        $stmt = $this->pdo->prepare('DELETE FROM agences WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}