<?php
$utilisateurConnecte = $_SESSION['utilisateur'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header>
    <h1>Touche pas au klaxon</h1>
    <p>Le covoiturage, simplement et sereinement.</p>

        <nav class="navigation-principale">
        <a href="/">Accueil</a>
        <a href="/trajets">Rechercher un trajet</a>

        <?php if ($utilisateurConnecte): ?>
            <a href="/proposer-trajet">Proposer un trajet</a>
            <a href="/mes-trajets">Mes trajets</a>
            <a href="/mes-reservations">Mes réservations</a>

            <?php if (($utilisateurConnecte['role'] ?? '') === 'admin'): ?>
                <a href="/admin">Administration</a>
            <?php endif; ?>

            <span class="salutation">
                Bonjour,
                <strong><?= htmlspecialchars(
                    $utilisateurConnecte['prenom'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></strong> !
            </span>

            <form action="/deconnexion" method="POST">
                <button class="btn" type="submit">Se déconnecter</button>
            </form>
        <?php else: ?>
            <a href="/inscription">Créer un compte</a>
            <a href="/connexion">Se connecter</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <section class="trajet-card">
        <h2>Bienvenue !</h2>

        <p>
            Partagez vos trajets, rencontrez d'autres voyageurs
            et prenez la route ensemble.
        </p>

        <h2>Rechercher un trajet</h2>

        <form action="/trajets" method="GET">
            <p>
                <label for="depart"><strong>Ville de départ</strong></label><br>
                <select id="depart" name="depart">
                    <option value="">Toutes les villes</option>
                    <?php foreach ($agences as $agence): ?>
                        <option value="<?= (int) $agence['id'] ?>">
                            <?= htmlspecialchars($agence['ville'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="arrivee"><strong>Ville d'arrivée</strong></label><br>
                <select id="arrivee" name="arrivee">
                    <option value="">Toutes les villes</option>
                    <?php foreach ($agences as $agence): ?>
                        <option value="<?= (int) $agence['id'] ?>">
                            <?= htmlspecialchars($agence['ville'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="date"><strong>Date de départ</strong></label><br>
                <input type="date" id="date" name="date">
            </p>

            <button class="btn" type="submit">Rechercher</button>
        </form>

        <p>
            <a class="retour" href="/trajets">Voir tous les trajets</a>
        </p>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Touche pas au klaxon — Covoiturons ensemble.</p>
</footer>

</body>
</html>
