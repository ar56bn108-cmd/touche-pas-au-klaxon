<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agence;
use InvalidArgumentException;
use PDO;
use Throwable;

class AgenceController
{
    public function __construct(private PDO $pdo)
    {
    }

    private function verifierAdmin(): bool
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
            echo 'Accès interdit.';
            return false;
        }

        return true;
    }

    public function index(): void
    {
        if (!$this->verifierAdmin()) {
            return;
        }

        $agenceModel = new Agence($this->pdo);
        $message = $_SESSION['message_agence'] ?? '';
        unset($_SESSION['message_agence']);

        $erreur = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            try {
                if ($action === 'creer') {
                    $ville = trim((string) ($_POST['ville'] ?? ''));
                    $agenceModel->creer($ville);
                    $_SESSION['message_agence'] = 'Agence créée avec succès.';
                } elseif ($action === 'modifier') {
                    $id = filter_var(
                        $_POST['id'] ?? null,
                        FILTER_VALIDATE_INT
                    );
                    $ville = trim((string) ($_POST['ville'] ?? ''));

                    if ($id === false || $id <= 0) {
                        throw new InvalidArgumentException('Identifiant invalide.');
                    }

                    $agenceModel->modifier($id, $ville);
                    $_SESSION['message_agence'] = 'Modification enregistrée.';
                } elseif ($action === 'supprimer') {
                    $id = filter_var(
                        $_POST['id'] ?? null,
                        FILTER_VALIDATE_INT
                    );

                    if ($id === false || $id <= 0) {
                        throw new InvalidArgumentException('Identifiant invalide.');
                    }

                    $agenceModel->supprimer($id);
                    $_SESSION['message_agence'] = 'Agence supprimée avec succès.';
                } else {
                    throw new InvalidArgumentException('Action inconnue.');
                }

                header('Location: /admin/agences');
                exit;
            } catch (InvalidArgumentException $e) {
                $erreur = $e->getMessage();
            } catch (Throwable $e) {
                error_log($e->getMessage());
                $erreur = 'Une erreur est survenue. Vérifie les données et réessaie.';
            }
        }

        $agences = $agenceModel->getAll();

        require __DIR__ . '/../Views/agences-admin.php';
    }
}