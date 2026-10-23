<?php
declare(strict_types=1);

function e(string $valeur): string {
    return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposer un trajet - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>Touche pas au klaxon</h1>
        <p>Le covoiturage, simplement.</p>
        <nav>
            <a href="/">Accueil</a>
            <a href="/trajets">Voir les trajets</a>
        </nav>
    </header>

    <main class="auth-container">
        <h1>Proposer un trajet</h1>
        <p>Partage ton trajet et propose des places aux autres utilisateurs.</p>

        <?php if ($erreur !== ''): ?>
            <p class="message-erreur" role="alert"><?= e($erreur) ?></p>
        <?php endif; ?>

        <form action="/proposer-trajet" method="post">
            <div class="form-group">
                <label for="agence_depart_id">Agence de départ</label>
                <select id="agence_depart_id" name="agence_depart_id" required>
                    <option value="">Choisir une agence</option>
                    <?php foreach ($agences as $agence): ?>
                        <option value="<?= (int) $agence['id'] ?>"
                            <?= $departId === (int) $agence['id'] ? 'selected' : '' ?>>
                            <?= e((string) $agence['ville']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="agence_arrivee_id">Agence d'arrivée</label>
                <select id="agence_arrivee_id" name="agence_arrivee_id" required>
                    <option value="">Choisir une agence</option>
                    <?php foreach ($agences as $agence): ?>
                        <option value="<?= (int) $agence['id'] ?>"
                            <?= $arriveeId === (int) $agence['id'] ? 'selected' : '' ?>>
                            <?= e((string) $agence['ville']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_depart">Date de départ</label>
                <input type="date" id="date_depart" name="date_depart"
                    value="<?= e($dateDepart) ?>" required>
            </div>

            <div class="form-group">
                <label for="heure_depart">Heure de départ</label>
                <input type="time" id="heure_depart" name="heure_depart"
                    value="<?= e($heureDepart) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_arrivee">Date d'arrivée</label>
                <input type="date" id="date_arrivee" name="date_arrivee"
                    value="<?= e($dateArrivee) ?>" required>
            </div>

            <div class="form-group">
                <label for="heure_arrivee">Heure d'arrivée</label>
                <input type="time" id="heure_arrivee" name="heure_arrivee"
                    value="<?= e($heureArrivee) ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_places">Nombre de places disponibles</label>
                <input type="number" id="nombre_places" name="nombre_places"
                    min="1" max="255" value="<?= (int) $nombrePlaces ?>" required>
            </div>

            <button type="submit">Publier mon trajet</button>
        </form>

        <a class="retour" href="/trajets">← Retour aux trajets</a>
    </main>

    <footer>
        <p>Touche pas au klaxon — Covoiturons ensemble.</p>
    </footer>
</body>
</html>