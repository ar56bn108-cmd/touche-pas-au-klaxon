-- =====================================================
-- Projet : Touche pas au klaxon
-- Fichier : schema.sql
-- Objet : Création du schéma de la base de données
-- =====================================================

CREATE DATABASE IF NOT EXISTS touche_pas_klaxon
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE touche_pas_klaxon;

SET NAMES utf8mb4;

-- =====================================================
-- TABLE : utilisateurs
-- =====================================================

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) DEFAULT NULL,
    role ENUM('utilisateur', 'admin') NOT NULL DEFAULT 'utilisateur',
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE : agences
-- =====================================================

CREATE TABLE IF NOT EXISTS agences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ville VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE : trajets
-- =====================================================

CREATE TABLE IF NOT EXISTS trajets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT UNSIGNED NOT NULL,
    agence_depart_id INT UNSIGNED NOT NULL,
    agence_arrivee_id INT UNSIGNED NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    date_arrivee DATE NOT NULL,
    heure_arrivee TIME NOT NULL,
    nombre_places_total TINYINT UNSIGNED NOT NULL,
    nombre_places_disponibles TINYINT UNSIGNED NOT NULL,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_trajets_date_depart (date_depart, heure_depart),
    KEY idx_trajets_utilisateur (utilisateur_id),

    CONSTRAINT fk_trajets_utilisateur
        FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_trajets_agence_depart
        FOREIGN KEY (agence_depart_id)
        REFERENCES agences(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_trajets_agence_arrivee
        FOREIGN KEY (agence_arrivee_id)
        REFERENCES agences(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_agences_differentes
        CHECK (agence_depart_id <> agence_arrivee_id),

    CONSTRAINT chk_nombre_places
        CHECK (
            nombre_places_total >= 1
            AND nombre_places_disponibles <= nombre_places_total
        ),

    CONSTRAINT chk_date_arrivee
        CHECK (
            TIMESTAMP(date_arrivee, heure_arrivee)
            > TIMESTAMP(date_depart, heure_depart)
        )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE : reservations
-- =====================================================

CREATE TABLE IF NOT EXISTS reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trajet_id INT UNSIGNED NOT NULL,
    utilisateur_id INT UNSIGNED NOT NULL,
    nombre_places TINYINT UNSIGNED NOT NULL DEFAULT 1,
    date_reservation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_reservation_trajet_utilisateur (trajet_id, utilisateur_id),
    KEY idx_reservations_trajet (trajet_id),
    KEY idx_reservations_utilisateur (utilisateur_id),

    CONSTRAINT fk_reservations_trajet
        FOREIGN KEY (trajet_id)
        REFERENCES trajets(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reservations_utilisateur
        FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;