<?php
declare(strict_types=1);

$utilisateur = $_SESSION['utilisateur'] ?? [];

function eReservation(mixed $valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes réservations - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header>
    <h1>Touche pas au klaxon</h1>
    <p>Retrouvez et gérez vos réservations de covoiturage.</p>
    <nav>
        <p>Bonjour, <strong><?= eReservation($utilisateur['prenom'] ?? '') ?></strong> !</p>
        <p>
            <a href="/">Accueil</a>
            <a href="/trajets">Rechercher un trajet</a>
        </p>
    </nav>
</header>

<main>
    <section class="trajet-card">
        <h2>Mes réservations</h2>
        <p>Consultez vos trajets réservés et gérez vos places.</p>

        <?php if (($_GET['annulation'] ?? '') === 'success'): ?>
            <p role="status" style="color: #176b5b;">
                Votre réservation a été annulée et les places ont été restituées.
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['erreur'])): ?>
            <p role="alert" style="color: #a52a2a;">
                Impossible d'annuler cette réservation. Actualisez la page et réessayez.
            </p>
        <?php endif; ?>

        <?php if (empty($reservations)): ?>
            <p>Vous n'avez aucune réservation pour le moment.</p>
            <p>
                <a class="btn" href="/trajets">Découvrir les trajets disponibles</a>
            </p>
        <?php else: ?>
            <?php foreach ($reservations as $reservation): ?>
                <article class="trajet-card">
                    <h3>
                        <?= eReservation($reservation['ville_depart']) ?>
                        &rarr;
                        <?= eReservation($reservation['ville_arrivee']) ?>
                    </h3>

                    <p>
                        <strong>Départ :</strong>
                        <?= eReservation(date('d/m/Y', strtotime((string) $reservation['date_depart']))) ?>
                        à <?= eReservation(substr((string) $reservation['heure_depart'], 0, 5)) ?>
                    </p>

                    <p>
                        <strong>Arrivée :</strong>
                        <?= eReservation(date('d/m/Y', strtotime((string) $reservation['date_arrivee']))) ?>
                        à <?= eReservation(substr((string) $reservation['heure_arrivee'], 0, 5)) ?>
                    </p>

                    <p>
                        <strong>Conducteur :</strong>
                        <?= eReservation($reservation['conducteur_prenom']) ?>
                        <?= eReservation($reservation['conducteur_nom']) ?>
                    </p>

                    <p>
                        <strong>Places réservées :</strong>
                        <?= (int) $reservation['nombre_places'] ?>
                    </p>

                    <p>
                        <a class="btn" href="/trajet?id=<?= (int) $reservation['trajet_id'] ?>">
                            Voir le trajet
                        </a>
                    </p>

                    <form method="POST" action="/annuler-reservation"
                          onsubmit="return confirm('Confirmer l’annulation de cette réservation ?');">
                        <input type="hidden" name="reservation_id"
                               value="<?= (int) $reservation['reservation_id'] ?>">
                        <button type="submit">Annuler ma réservation</button>
                    </form>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <p><a class="retour" href="/">← Retour à l’accueil</a></p>
    </section>
</main>

</body>
</html>