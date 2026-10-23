<?php
declare(strict_types=1);

use App\Models\Agence;
use PHPUnit\Framework\TestCase;

final class AgenceTest extends TestCase
{
    private PDO $pdo;
    private Agence $agence;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            'mysql:host=127.0.0.1;port=3309;dbname=touche_pas_klaxon_test;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $this->pdo->exec('DELETE FROM reservations');
        $this->pdo->exec('DELETE FROM trajets');
        $this->pdo->exec('DELETE FROM agences');
        $this->pdo->exec('DELETE FROM utilisateurs');

        $this->agence = new Agence($this->pdo);
    }

    public function testCreerAgence(): void
    {
        self::assertTrue($this->agence->creer('Toulouse'));

        $stmt = $this->pdo->prepare(
            'SELECT ville FROM agences WHERE ville = :ville'
        );
        $stmt->execute(['ville' => 'Toulouse']);

        self::assertSame('Toulouse', $stmt->fetchColumn());
    }

    public function testRefuserVilleEnDouble(): void
    {
        $this->agence->creer('Toulouse');

        $this->expectException(InvalidArgumentException::class);

        $this->agence->creer('Toulouse');
    }

    public function testRefuserVilleVide(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->agence->creer('   ');
    }

    public function testModifierAgence(): void
    {
        $this->agence->creer('Toulouse');
        $id = (int) $this->pdo->lastInsertId();

        self::assertTrue($this->agence->modifier($id, 'Bordeaux'));

        $stmt = $this->pdo->prepare(
            'SELECT ville FROM agences WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        self::assertSame('Bordeaux', $stmt->fetchColumn());
    }

    public function testSupprimerAgence(): void
    {
        $this->agence->creer('Toulouse');
        $id = (int) $this->pdo->lastInsertId();

        self::assertTrue($this->agence->supprimer($id));

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM agences WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        self::assertSame(0, (int) $stmt->fetchColumn());
    }

    public function testRefuserSuppressionIdentifiantInvalide(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->agence->supprimer(0);
    }

    public function testRefuserSuppressionAgenceUtiliseeParUnTrajet(): void
    {
        $this->agence->creer('Toulouse');
        $departId = (int) $this->pdo->lastInsertId();

        $this->agence->creer('Bordeaux');
        $arriveeId = (int) $this->pdo->lastInsertId();

        $this->pdo->exec(
            "INSERT INTO utilisateurs (nom, prenom, telephone, email)
             VALUES ('Test', 'Conducteur', '0600000000', 'conducteur-test@example.com')"
        );
        $utilisateurId = (int) $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare(
            'INSERT INTO trajets (
                utilisateur_id, agence_depart_id, agence_arrivee_id,
                date_depart, heure_depart, date_arrivee, heure_arrivee,
                nombre_places_total, nombre_places_disponibles
            ) VALUES (
                :utilisateur_id, :depart, :arrivee,
                :date_depart, :heure_depart, :date_arrivee, :heure_arrivee,
                3, 3
            )'
        );

        $stmt->execute([
            'utilisateur_id' => $utilisateurId,
            'depart' => $departId,
            'arrivee' => $arriveeId,
            'date_depart' => '2099-06-10',
            'heure_depart' => '10:00:00',
            'date_arrivee' => '2099-06-10',
            'heure_arrivee' => '12:00:00',
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->agence->supprimer($departId);
    }
}