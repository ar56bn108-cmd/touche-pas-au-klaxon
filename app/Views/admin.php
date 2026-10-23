<?php
declare(strict_types=1);

function adminEchapper(mixed $valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="container" style="max-width: 1200px; margin: 30px auto; padding: 20px;">
        <h1>Administration</h1>
        <p>Bienvenue dans le tableau de bord administrateur.</p>

        <h2>Utilisateurs (<?= count($utilisateurs) ?>)</h2>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nom</th><th>Prénom</th>
                        <th>Email</th><th>Téléphone</th><th>Rôle</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?= adminEchapper($utilisateur['id'] ?? '') ?></td>
                        <td><?= adminEchapper($utilisateur['nom'] ?? '') ?></td>
                        <td><?= adminEchapper($utilisateur['prenom'] ?? '') ?></td>
                        <td><?= adminEchapper($utilisateur['email'] ?? '') ?></td>
                        <td><?= adminEchapper($utilisateur['telephone'] ?? '') ?></td>
                        <td><?= adminEchapper($utilisateur['role'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2>Trajets (<?= count($trajets) ?>)</h2>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Conducteur</th><th>Départ</th>
                        <th>Arrivée</th><th>Date</th><th>Places disponibles</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($trajets as $trajet): ?>
                    <tr>
                        <td><?= adminEchapper($trajet['id'] ?? '') ?></td>
                        <td><?= adminEchapper(($trajet['prenom'] ?? '') . ' ' . ($trajet['nom'] ?? '')) ?></td>
                        <td><?= adminEchapper($trajet['ville_depart'] ?? '') ?></td>
                        <td><?= adminEchapper($trajet['ville_arrivee'] ?? '') ?></td>
                        <td><?= adminEchapper($trajet['date_depart'] ?? '') ?></td>
                        <td><?= adminEchapper($trajet['nombre_places_disponibles'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p><a href="/admin/agences">Gérer les agences</a></p>
<p><a href="/">Retour à l'accueil</a></p>
    </main>
</body>
</html>