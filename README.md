# Touche pas au klaxon

Application de covoiturage développée en PHP selon l'architecture MVC, avec une base de données MySQL/MariaDB.

## Fonctionnalités

- Inscription, connexion et déconnexion.
- Recherche et consultation des trajets.
- Création, modification et suppression de ses propres trajets.
- Réservation et annulation de réservation.
- Gestion des agences réservée aux administrateurs.
- Tableau de bord d'administration.
- Messages de confirmation après les opérations.

## Technologies

- PHP 8.2
- MySQL / MariaDB
- PDO
- Composer
- PHPUnit
- PHPStan
- Bootstrap et Sass

## Prérequis

- XAMPP ou un environnement PHP/MySQL équivalent.
- Composer.
- MySQL ou MariaDB.

## Installation

### 1. Installer les dépendances

Depuis la racine du projet :

```bash
composer install
```

### 2. Configurer la base de données

Les paramètres de connexion se trouvent dans `config/database.php`.

Configuration actuelle de développement :

- Hôte : `127.0.0.1`
- Port MySQL : `3309`
- Base de données : `touche_pas_klaxon`
- Utilisateur : `root`
- Mot de passe : vide dans l'environnement local XAMPP

Adaptez ces paramètres à votre environnement. En production, utilisez un compte dédié et un mot de passe sécurisé.

Le fichier `database/schema.sql` définit les tables nécessaires à une installation neuve. **Ne pas exécuter ce fichier sur une base existante sans vérifier son contenu et effectuer une sauvegarde.**

### 3. Charger les données initiales

Après avoir créé une base neuve, exécutez `database/schema.sql`, puis `database/seed.sql`.

Le fichier `database/seed.sql` contient les agences et les utilisateurs de démonstration. Les utilisateurs ajoutés par ce script n'ont pas de mot de passe initialisé automatiquement.

### 4. Créer un compte administrateur de démonstration

Sur une installation neuve :

1. Démarrer MySQL et l'application.
2. Ouvrir le formulaire d'inscription sur le site.
3. Créer un compte avec l'adresse `test@gmail.com`, un nom, un prénom, un téléphone et un mot de passe d'au moins 8 caractères.
4. Dans MySQL, attribuer le rôle `admin` à ce compte en exécutant la requête suivante :

```sql
UPDATE utilisateurs
SET role = 'admin'
WHERE email = 'test@gmail.com';
```

5. Vérifier que le compte existe et possède le bon rôle :

```sql
SELECT id, nom, prenom, email, role
FROM utilisateurs
WHERE email = 'test@gmail.com';
```

La requête de mise à jour doit modifier un compte. Si aucun compte n'est trouvé, vérifier l'inscription et l'adresse e-mail avant de continuer.

Le mot de passe est défini lors de l'inscription et stocké sous forme hachée par l'application. Aucun mot de passe n'est fourni dans ce README.

**Important :** cette procédure est destinée à l'initialisation locale d'une installation neuve. Ne pas attribuer le rôle administrateur à un compte non vérifié.

### 5. Démarrer le serveur

Depuis la racine du projet :

```bash
php -S 127.0.0.1:8000 -t public
```

Ouvrir ensuite [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Tests et qualité du code

Lancer les tests :

```bash
composer test
```

Lancer l'analyse statique :

```bash
composer analyse
```

Dernière vérification documentée : 8 tests réussis et aucune erreur PHPStan.

## Administration

L'espace d'administration est accessible à l'adresse :

[http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

Un compte doit posséder le rôle `admin` dans la base de données pour accéder à cet espace.

## Structure du projet

- `app/` : application et modèles MVC.
- `config/` : configuration.
- `database/` : schéma SQL et données initiales.
- `docs/` : documentation et schémas MCD/MLD.
- `public/` : point d'entrée et ressources publiques.
- `routes/` : routes de l'application.
- `tests/` : tests automatisés.

## Livraison et démonstration

- **MCD et MLD :** voir `docs/MCD_MLD.md`.
- **Image du MCD :** `docs/MCD.png`.
- **Compte de démonstration :** `test@gmail.com`, à créer et promouvoir selon la procédure ci-dessus sur une installation neuve.
- **Accès administrateur :** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin).
- **Sécurité :** aucun mot de passe ne doit être enregistré dans ce README.
- **Dépôt GitHub :** https://github.com/ar56bn108-cmd/touche-pas-au-klaxon

Avant la livraison, vérifier l'installation sur une base neuve, les identifiants de démonstration et la lisibilité du schéma MCD.
