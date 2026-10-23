<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du trajet - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="container">
        <section class="trajet-card">
            <h1>Détail du trajet</h1>

            <h2>
                <?= htmlspecialchars((string) $trajet['ville_depart'], ENT_QUOTES, 'UTF-8') ?>
                →
                <?= htmlspecialchars((string) $trajet['ville_arrivee'], ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <p>
                <strong>Conducteur :</strong>
                <?= htmlspecialchars(
                    trim(($trajet['prenom'] ?? '') . ' ' . ($trajet['nom'] ?? '')),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </p>

            <p>
                <strong>Date de départ :</strong>
                <?= date('d/m/Y', strtotime($trajet['date_depart'])) ?>
            </p>

            <p>
                <strong>Heure de départ :</strong>
                <?= htmlspecialchars(substr($trajet['heure_depart'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <strong>Date d'arrivée :</strong>
                <?= date('d/m/Y', strtotime($trajet['date_arrivee'])) ?>
            </p>

            <p>
                <strong>Heure d'arrivée :</strong>
                <?= htmlspecialchars(substr($trajet['heure_arrivee'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <strong>Places disponibles :</strong>
                <?= (int) $trajet['nombre_places_disponibles'] ?>
                /
                <?= (int) $trajet['nombre_places_total'] ?>
            </p>

            <?php if (isset($_GET['reservation']) && $_GET['reservation'] === 'success'): ?>
                <p class="success">Ta réservation a bien été enregistrée !</p>
            <?php elseif (!empty($aDejaReserve) && !$estConducteur): ?>
                <p class="success">Tu as déjà réservé ce trajet.</p>
            <?php endif; ?>

            <?php if ($estConducteur): ?>

                <p>Tu es le conducteur de ce trajet : tu ne peux pas le réserver.</p>

            <?php elseif ($aDejaReserve): ?>

                <p class="success">Tu as déjà réservé ce trajet.</p>

            <?php elseif (empty($utilisateur)): ?>

                <p>Connecte-toi pour réserver une place sur ce trajet.</p>
                <p><a class="btn" href="/connexion">Se connecter</a></p>

            <?php elseif ((int) $trajet['nombre_places_disponibles'] > 0): ?>

                <form action="/reservation" method="POST">
                    <input
                        type="hidden"
                        name="trajet_id"
                        value="<?= (int) $trajet['id'] ?>"
                    >

                    <label for="nombre_places">Nombre de places :</label>

                    <select id="nombre_places" name="nombre_places" required>
                        <?php for ($i = 1; $i <= (int) $trajet['nombre_places_disponibles']; $i++): ?>
                            <option value="<?= $i ?>">
                                <?= $i ?> place<?= $i > 1 ? 's' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" class="btn">Réserver</button>
                </form>

            <?php else: ?>

                <p>Ce trajet est complet.</p>

            <?php endif; ?>

            <p>
                <a class="retour" href="/trajets">Retour à la liste des trajets</a>
            </p>

            <p>
                <a class="retour" href="/">Retour à l'accueil</a>
            </p>
        </section>
    </main>
</body>
</html>