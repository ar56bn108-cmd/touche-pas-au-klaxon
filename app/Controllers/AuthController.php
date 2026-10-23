<?php
declare(strict_types=1);

namespace App\Controllers;

use PDO;
use PDOException;

class AuthController
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function inscription(): void
    {
        $erreurs = [];
        $nom = '';
        $prenom = '';
        $telephone = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $confirmation = $_POST['confirmation'] ?? '';

            if ($nom === '' || $prenom === '' || $telephone === '') {
                $erreurs[] = 'Tous les champs sont obligatoires.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'Adresse e-mail invalide.';
            }

            if (strlen($motDePasse) < 8) {
                $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }

            if ($motDePasse !== $confirmation) {
                $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
            }

            if (empty($erreurs)) {
                $verification = $this->pdo->prepare(
                    'SELECT id FROM utilisateurs WHERE email = :email'
                );
                $verification->execute(['email' => $email]);

                if ($verification->fetch()) {
                    $erreurs[] = 'Cette adresse e-mail est déjà utilisée.';
                } else {
                    $hash = password_hash($motDePasse, PASSWORD_DEFAULT);

                    try {
                        $requete = $this->pdo->prepare(
                            'INSERT INTO utilisateurs
                                (nom, prenom, telephone, email, mot_de_passe)
                             VALUES
                                (:nom, :prenom, :telephone, :email, :mot_de_passe)'
                        );

                        $requete->execute([
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'telephone' => $telephone,
                            'email' => $email,
                            'mot_de_passe' => $hash,
                        ]);

                        header('Location: /connexion?inscription=success');
                        exit;
                    } catch (PDOException $e) {
                        $erreurs[] = 'Impossible de créer le compte. Réessaie.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/inscription.php';
    }

    public function connexion(): void
    {
        $erreurs = [];
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $motDePasse = $_POST['mot_de_passe'] ?? '';

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $motDePasse === '') {
                $erreurs[] = 'E-mail ou mot de passe incorrect.';
            } else {
                $requete = $this->pdo->prepare(
                    'SELECT id, nom, prenom, email, mot_de_passe, role
                     FROM utilisateurs
                     WHERE email = :email
                     LIMIT 1'
                );
                $requete->execute(['email' => $email]);
                $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

                if (
                    !$utilisateur ||
                    empty($utilisateur['mot_de_passe']) ||
                    !password_verify($motDePasse, $utilisateur['mot_de_passe'])
                ) {
                    $erreurs[] = 'E-mail ou mot de passe incorrect.';
                } else {
                    session_regenerate_id(true);

                    $_SESSION['utilisateur'] = [
                        'id' => (int) $utilisateur['id'],
                        'nom' => $utilisateur['nom'],
                        'prenom' => $utilisateur['prenom'],
                        'email' => $utilisateur['email'],
                        'role' => $utilisateur['role'],
                    ];

                    header('Location: /');
                    exit;
                }
            }
        }

        require __DIR__ . '/../Views/connexion.php';
    }

    public function deconnexion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée.';
            return;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametres = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametres['path'],
                $parametres['domain'],
                $parametres['secure'],
                $parametres['httponly']
            );
        }

        session_destroy();

        header('Location: /');
        exit;
    }
}
