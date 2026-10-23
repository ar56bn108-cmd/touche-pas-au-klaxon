
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos trajets - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header>
    <h1>Touche pas au klaxon</h1>
    <p>Le covoiturage, simplement et sereinement.</p>
</header>

<main>
    <h2>Les trajets de covoiturage</h2>

    <?php if (empty($trajets)): ?>
        <p>Aucun trajet n'est disponible pour le moment.</p>
    <?php else: ?>

        <?php foreach ($trajets as $trajet): ?>
            <article class="trajet-card">
                <h3>
                    <?= htmlspecialchars($trajet['ville_depart'], ENT_QUOTES, 'UTF-8') ?>
                    →
                    <?= htmlspecialchars($trajet['ville_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <p>
                    <strong>📅 Départ :</strong>
                    <?= date('d/m/Y', strtotime($trajet['date_depart'])) ?>
                    à
                    <?= htmlspecialchars(substr($trajet['heure_depart'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>🕒 Arrivée :</strong>
                    <?= date('d/m/Y', strtotime($trajet['date_arrivee'])) ?>
                    à
                    <?= htmlspecialchars(substr($trajet['heure_arrivee'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>💺 Places disponibles :</strong>
                    <?= (int) $trajet['nombre_places_disponibles'] ?>
                    /
                    <?= (int) $trajet['nombre_places_total'] ?>
                </p>

                <p>
                    <strong>🚗 Conducteur :</strong>
                    <?= htmlspecialchars(
                        $trajet['prenom'] . ' ' . $trajet['nom'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p>
                    <a class="btn" href="/trajet?id=<?= (int) $trajet['id'] ?>">
                        Voir le trajet
                    </a>
                </p>
            </article>
        <?php endforeach; ?>

    <?php endif; ?>

    <a class="retour" href="/">← Retour à l'accueil</a>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Touche pas au klaxon — Covoiturons ensemble.</p>
</footer>

</body>
</html>
