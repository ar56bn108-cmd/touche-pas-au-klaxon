
<?php
$erreurs = $erreurs ?? [];
$email = $email ?? '';

$inscriptionReussie = isset($_GET['inscription'])
    && $_GET['inscription'] === 'success';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="auth-container">
        <h1>Connexion</h1>
        <p>Content de te revoir sur Touche pas au klaxon !</p>

        <?php if ($inscriptionReussie): ?>
            <p class="message-succes">
                Ton compte a été créé. Tu peux maintenant te connecter.
            </p>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="message-erreur">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/connexion" method="POST">
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit">Me connecter</button>
        </form>

        <p>Tu n’as pas encore de compte ?
            <a href="/inscription">Créer un compte</a>
        </p>

        <p><a href="/">Retour à l’accueil</a></p>
    </main>
</body>
</html>
