<?php
$echapper = static fn ($valeur): string =>
    htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');

$dateDepart = substr((string) $trajet['date_depart'], 0, 10);
$dateArrivee = substr((string) $trajet['date_arrivee'], 0, 10);
$heureDepart = substr((string) $trajet['heure_depart'], 0, 5);
$heureArrivee = substr((string) $trajet['heure_arrivee'], 0, 5);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un trajet - Touche pas au klaxon</title>
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <main class="container">
        <h1>Modifier mon trajet</h1>

        <?php if (!empty($erreur)): ?>
            <p class="message-erreur">
                <?= $echapper($erreur) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="/modifier-trajet?id=<?= (int) $trajet['id'] ?>">
            <div class="form-group">
                <label for="agence_depart_id">Ville de départ</label>
                <select id="agence_depart_id" name="agence_depart_id" required>
                    <?php foreach ($agences as $agence): ?>
                        <option
                            value="<?= (int) $agence['id'] ?>"
                            <?= (int) $agence['id'] === (int) $trajet['agence_depart_id'] ? 'selected' : '' ?>
                        >
                            <?= $echapper($agence['ville']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

                        <div class="form-group">
                <label for="agence_arrivee_id">Ville d’arrivée</label>
                <select id="agence_arrivee_id" name="agence_arrivee_id" required>
                    <?php foreach ($agences as $agence): ?>
                        <option
                            value="<?= (int) $agence['id'] ?>"
                            <?= (int) $agence['id'] === (int) $trajet['agence_arrivee_id'] ? 'selected' : '' ?>
                        >
                            <?= $echapper($agence['ville']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date_depart">Date de départ</label>
                <input type="date" id="date_depart" name="date_depart"
                       value="<?= $echapper($dateDepart) ?>" required>
            </div>

            <div class="form-group">
                <label for="heure_depart">Heure de départ</label>
                <input type="time" id="heure_depart" name="heure_depart"
                       value="<?= $echapper($heureDepart) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_arrivee">Date d’arrivée</label>
                <input type="date" id="date_arrivee" name="date_arrivee"
                       value="<?= $echapper($dateArrivee) ?>" required>
            </div>

            <div class="form-group">
                <label for="heure_arrivee">Heure d’arrivée</label>
                <input type="time" id="heure_arrivee" name="heure_arrivee"
                       value="<?= $echapper($heureArrivee) ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_places_total">Nombre total de places</label>
                <input type="number" id="nombre_places_total"
                       name="nombre_places_total" min="1" max="255"
                       value="<?= (int) $trajet['nombre_places_total'] ?>" required>
            </div>

            <button type="submit">Enregistrer les modifications</button>
            <a class="btn-secondaire" href="/mes-trajets">Annuler</a>
        </form>
    </main>
</body>
</html>