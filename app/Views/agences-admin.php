<?php
declare(strict_types=1);

function agenceAdminEchapper(mixed $valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des agences - Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="container" style="max-width: 1000px; margin: 30px auto; padding: 20px;">
    <h1>Gestion des agences</h1>

    <?php if (!empty($message)): ?>
        <p role="status" style="color: green;">
            <?= agenceAdminEchapper($message) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($erreur)): ?>
        <p role="alert" style="color: #b00020;">
            <?= agenceAdminEchapper($erreur) ?>
        </p>
    <?php endif; ?>

    <section>
        <h2>Ajouter une agence</h2>
        <form method="post" action="/admin/agences">
            <input type="hidden" name="action" value="creer">
            <label for="nouvelle-ville">Nom de la ville</label>
            <input
                id="nouvelle-ville"
                type="text"
                name="ville"
                maxlength="100"
                required
            >
            <button type="submit">Ajouter</button>
        </form>
    </section>

    <section>
        <h2>Agences existantes (<?= count($agences) ?>)</h2>

        <?php if ($agences === []): ?>
            <p>Aucune agence enregistrée.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ville</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($agences as $agence): ?>
                        <tr>
                            <td><?= agenceAdminEchapper($agence['id'] ?? '') ?></td>
                            <td><?= agenceAdminEchapper($agence['ville'] ?? '') ?></td>
                            <td>
                                <form method="post" action="/admin/agences">
                                    <input type="hidden" name="action" value="modifier">
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= agenceAdminEchapper($agence['id'] ?? '') ?>"
                                    >
                                    <label>
                                        <span class="visually-hidden">Nouveau nom de ville</span>
                                        <input
                                            type="text"
                                            name="ville"
                                            maxlength="100"
                                            value="<?= agenceAdminEchapper($agence['ville'] ?? '') ?>"
                                            required
                                        >
                                    </label>
                                    <button type="submit">Enregistrer</button>
                                </form>
                            </td>
                            <td>
                                <form
                                    method="post"
                                    action="/admin/agences"
                                    onsubmit="return confirm('Confirmer la suppression de cette agence ?');"
                                >
                                    <input type="hidden" name="action" value="supprimer">
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= agenceAdminEchapper($agence['id'] ?? '') ?>"
                                    >
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <p><a href="/admin">Retour au tableau de bord</a></p>
</main>
</body>
</html>