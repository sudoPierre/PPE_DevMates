-- =============================================
-- FICHIER  : database.sql
-- RÔLE     : Crée la base de données DevMates,
--            les tables, le trigger et quelques
--            données de test
-- UTILISATION : Importer dans phpMyAdmin ou via
--               MySQL CLI : mysql -u root < database.sql
-- =============================================

-- Création de la base de données (si elle n'existe pas déjà)
CREATE DATABASE IF NOT EXISTS devmates
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Sélectionner la base de données
USE devmates;

-- =============================================
-- TABLE : users
-- Stocke les informations de chaque développeur
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nom              VARCHAR(100)        NOT NULL,
    prenom           VARCHAR(100)        NOT NULL,
    email            VARCHAR(150)        NOT NULL UNIQUE,
    mot_de_passe     VARCHAR(255)        NOT NULL,
    role             ENUM('dev','admin') NOT NULL DEFAULT 'dev',
    competences      TEXT                COMMENT 'Compétences séparées par des virgules : PHP,Python,JS',
    bio              TEXT,
    date_inscription DATETIME            DEFAULT NOW()
) ENGINE=InnoDB;

-- =============================================
-- TABLE : messages
-- Stocke les messages échangés entre utilisateurs
-- =============================================
CREATE TABLE IF NOT EXISTS messages (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    id_expediteur    INT        NOT NULL,
    id_destinataire  INT        NOT NULL,
    contenu          TEXT       NOT NULL,
    date_envoi       DATETIME   DEFAULT NOW(),
    lu               TINYINT(1) DEFAULT 0      COMMENT '0 = non lu, 1 = lu',
    FOREIGN KEY (id_expediteur)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_destinataire) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TABLE : matches
-- Stocke les scores de compatibilité calculés
-- entre deux développeurs
-- =============================================
CREATE TABLE IF NOT EXISTS matches (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    id_user1            INT   NOT NULL,
    id_user2            INT   NOT NULL,
    score_compatibilite FLOAT DEFAULT 0,
    date_match          DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_user1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user2) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TRIGGER : before_user_insert
-- Exécuté AVANT chaque insertion dans users
-- Met automatiquement date_inscription à NOW()
-- =============================================
DROP TRIGGER IF EXISTS before_user_insert;

DELIMITER //
CREATE TRIGGER before_user_insert
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    -- Forcer la date d'inscription à l'heure actuelle
    SET NEW.date_inscription = NOW();
END;
//
DELIMITER ;

-- =============================================
-- DONNÉES DE TEST
-- ⚠️  Les mots de passe sont hashés avec PHP
--     password_hash('devmates123', PASSWORD_DEFAULT)
-- ⚠️  Pour créer ces utilisateurs avec des vrais
--     hash, exécutez setup.php depuis un navigateur
--     ou en ligne de commande : php setup.php
-- =============================================

-- Données de test : 3 développeurs + 1 admin
-- Mot de passe pour tous : devmates123
INSERT INTO users (nom, prenom, email, mot_de_passe, role, competences, bio) VALUES
(
    'Martin', 'Alice', 'alice@devmates.fr',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lN36',
    'dev',
    'PHP,Python,MySQL,Git',
    'Passionnée de développement web full-stack. J adore créer des APIs REST en PHP !'
),
(
    'Dupont', 'Bob', 'bob@devmates.fr',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lN36',
    'dev',
    'JavaScript,React,NodeJS,MongoDB',
    'Développeur frontend React. Je cherche des collabs sur des projets web modernes.'
),
(
    'Bernard', 'Clara', 'clara@devmates.fr',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lN36',
    'dev',
    'PHP,Java,Docker,MySQL,Linux',
    'Fan de DevOps et backend. J aime automatiser tout ce qui peut l être !'
),
(
    'Admin', 'Super', 'admin@devmates.fr',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lN36',
    'admin',
    '',
    'Administrateur de la plateforme DevMates.'
);

-- Message de test entre Alice (id=1) et Bob (id=2)
INSERT INTO messages (id_expediteur, id_destinataire, contenu, lu) VALUES
(1, 2, 'Salut Bob ! J ai vu que tu maîtrises React. Je cherche un partenaire pour un projet web !', 0),
(2, 1, 'Salut Alice ! Super, dis-moi en plus sur ton projet, je suis intéressé !', 0);
