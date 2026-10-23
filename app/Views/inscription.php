
<?php
$erreurs = $erreurs ?? [];
$nom = $nom ?? '';
$prenom = $prenom ?? '';
$telephone = $telephone ?? '';
$email = $email ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="auth-container">
        <h1>Créer un compte</h1>
        <p>Rejoins Touche pas au klaxon pour partager tes trajets.</p>

        <?php if (!empty($erreurs)): ?>
            <div class="message-erreur">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/inscription" method="POST">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    value="<?= htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="given-name"
                >
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    value="<?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="family-name"
                >
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    value="<?= htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="tel"
                >
            </div>

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
                <label for="mot_de_passe">Mot de passe (8 caractères minimum)</label>
                <input
                    type="password"
                    id="mot_de_passe"
                    name="mot_de_passe"
                    minlength="8"
                    required
                    autocomplete="new-password"
                >
            </div>

            <div class="form-group">
                <label for="confirmation">Confirmer le mot de passe</label>
                <input
                    type="password"
                    id="confirmation"
                    name="confirmation"
                    minlength="8"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit">Créer mon compte</button>
        </form>

        <p>Tu as déjà un compte ?
            <a href="/connexion">Connecte-toi</a>
        </p>

        <p><a href="/">Retour à l’accueil</a></p>
    </main>
</body>
</html>
