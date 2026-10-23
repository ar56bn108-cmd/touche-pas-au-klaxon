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

Le fichier `database/seed.sql` contient les agences et les utilisateurs de démonstration. Les utilisateurs ajoutés par ce script n'ont pas de mot de passe initialisé automatiquement.

### 4. Démarrer le serveur

Depuis la racine du projet :

```bash
php -S 127.0.0.1:8000 -t public
```

Ouvrir ensuite http://127.0.0.1:8000.

## Tests et qualité du code

Lancer les tests :

```bash
composer test
```

Lancer l'analyse statique :

```bash
composer analyse
```

Dernière vérification : 8 tests réussis et aucune erreur PHPStan.

## Administration

L'espace d'administration est accessible à l'adresse :

http://127.0.0.1:8000/admin

Un compte doit posséder le rôle `admin` dans la base de données pour accéder à cet espace.

## Structure du projet

- `app/` : application et modèles MVC.
- `config/` : configuration.
- `database/` : schéma SQL et données initiales.
- `public/` : point d'entrée et ressources publiques.
- `routes/` : routes de l'application.
- `tests/` : tests automatisés.
- `vendor/` : dépendances Composer.

## Livraison et démonstration

- **MCD et MLD :** voir `docs/MCD_MLD.md`.
- **Compte administrateur :** `test@gmail.com`.
- **Accès administrateur :** `http://127.0.0.1:8000/admin`.
- **Prérequis de connexion :** le compte doit disposer du rôle `admin` et d'un mot de passe valide.
- **Sécurité :** aucun mot de passe ne doit être enregistré dans ce README.
- **Dépôt GitHub :** ajouter l'URL lorsque le dépôt aura été créé.

Avant la livraison, vérifier l'installation sur une base neuve et confirmer les identifiants de démonstration.