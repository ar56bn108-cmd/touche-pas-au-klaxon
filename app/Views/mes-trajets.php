<?php
declare(strict_types=1);

if (!isset($trajets) || !is_array($trajets)) {
    $trajets = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes trajets - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php if (!empty($_SESSION['message_succes'])): ?>
        <p class="message-succes">
            <?= htmlspecialchars((string) $_SESSION['message_succes'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php unset($_SESSION['message_succes']); ?>
    <?php endif; ?>
    <?php if (isset($_GET['suppression']) && $_GET['suppression'] === 'success'): ?>
        <p class="message-succes">Trajet supprimé avec succès.</p>
    <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] === 'reservations'): ?>
        <p class="message-erreur">Ce trajet possède des réservations et ne peut pas être supprimé.</p>
    <?php elseif (isset($_GET['erreur'])): ?>
        <p class="message-erreur">La suppression n'a pas pu être effectuée.</p>
    <?php endif; ?>
    <main class="container">
        <h1>Mes trajets proposés</h1>

        <p>
            Retrouvez ici les trajets que vous avez proposés en tant que conducteur.
        </p>

        <nav class="navigation-mes-trajets" aria-label="Navigation des trajets">
            <a href="/">Accueil</a>
            <a href="/trajets">Voir tous les trajets</a>
            <a href="/proposer-trajet">Proposer un trajet</a>
        </nav>

        <?php if (empty($trajets)): ?>
            <p>Vous n'avez pas encore proposé de trajet.</p>
        <?php else: ?>
            <div class="trajets-list">
                <?php foreach ($trajets as $trajet): ?>
                    <article class="trajet-card">
                        <h2>
                            <?= htmlspecialchars((string) $trajet['ville_depart'], ENT_QUOTES, 'UTF-8') ?>
                            →
                            <?= htmlspecialchars((string) $trajet['ville_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p>
                            <strong>Départ :</strong>
                            <?= htmlspecialchars((string) $trajet['date_depart'], ENT_QUOTES, 'UTF-8') ?>
                            à
                            <?= htmlspecialchars(substr((string) $trajet['heure_depart'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p>
                            <strong>Arrivée :</strong>
                            <?= htmlspecialchars((string) $trajet['date_arrivee'], ENT_QUOTES, 'UTF-8') ?>
                            à
                            <?= htmlspecialchars(substr((string) $trajet['heure_arrivee'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p>
                            <strong>Places disponibles :</strong>
                            <?= (int) $trajet['nombre_places_disponibles'] ?>
                            /
                            <?= (int) $trajet['nombre_places_total'] ?>
                        </p>

                        <p>
                            <a class="lien-action" href="/trajet?id=<?= (int) $trajet['id'] ?>">
                                Voir le détail
                            </a>
                        </p>

                        <p>
                            <a class="lien-action" href="/modifier-trajet?id=<?= (int) $trajet['id'] ?>">
                                Modifier ce trajet
                            </a>
                        </p>

                        <form action="/supprimer-trajet" method="POST"
                              onsubmit="return confirm('Voulez-vous vraiment supprimer ce trajet ?');">
                            <input type="hidden" name="trajet_id"
                                   value="<?= (int) $trajet['id'] ?>">
                            <button type="submit">Supprimer ce trajet</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>