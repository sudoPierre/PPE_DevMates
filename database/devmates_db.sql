-- ==============================================================
-- Schéma de base de données DevMates
-- Plateforme de mise en relation développeurs / porteurs de projets
-- ==============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS devmates
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE devmates;

-- ==============================================================
-- Table des utilisateurs
-- Rôles : admin, developpeur, porteur_projet
-- ==============================================================
CREATE TABLE IF NOT EXISTS Users (
    id           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    email        VARCHAR(255)    NOT NULL,
    password_hash VARCHAR(255)   NOT NULL,
    role         ENUM('admin', 'developpeur', 'porteur_projet') NOT NULL DEFAULT 'developpeur',
    is_active    TINYINT(1)      NOT NULL DEFAULT 1,
    is_banned    TINYINT(1)      NOT NULL DEFAULT 0,
    created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- Table des profils utilisateurs
-- Compétences et idées de projets stockées en JSON
-- ==============================================================
CREATE TABLE IF NOT EXISTS Profiles (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED NOT NULL,
    display_name  VARCHAR(100) NOT NULL,
    bio           TEXT,
    skills        JSON         COMMENT 'Tableau JSON des compétences techniques',
    project_ideas JSON         COMMENT 'Tableau JSON des idées de projets',
    github_url    VARCHAR(255),
    linkedin_url  VARCHAR(255),
    avatar_url    VARCHAR(255),
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_profiles_user (user_id),
    CONSTRAINT fk_profile_user
        FOREIGN KEY (user_id) REFERENCES Users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- Table des matchs (swipes entre utilisateurs)
-- ==============================================================
CREATE TABLE IF NOT EXISTS Matches (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id_1    INT UNSIGNED NOT NULL,
    user_id_2    INT UNSIGNED NOT NULL,
    action_user_1 ENUM('like', 'dislike', 'pending') NOT NULL DEFAULT 'pending',
    action_user_2 ENUM('like', 'dislike', 'pending') NOT NULL DEFAULT 'pending',
    is_matched   TINYINT(1)   NOT NULL DEFAULT 0,
    matched_at   DATETIME,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_match_pair (user_id_1, user_id_2),
    CONSTRAINT fk_match_user1
        FOREIGN KEY (user_id_1) REFERENCES Users(id) ON DELETE CASCADE,
    CONSTRAINT fk_match_user2
        FOREIGN KEY (user_id_2) REFERENCES Users(id) ON DELETE CASCADE,
    CONSTRAINT chk_different_users CHECK (user_id_1 <> user_id_2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- Table des messages (conversations entre utilisateurs matchés)
-- ==============================================================
CREATE TABLE IF NOT EXISTS Messages (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    match_id  INT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    content   TEXT         NOT NULL,
    is_read   TINYINT(1)   NOT NULL DEFAULT 0,
    sent_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_messages_match_sent (match_id, sent_at),
    CONSTRAINT fk_message_match
        FOREIGN KEY (match_id) REFERENCES Matches(id) ON DELETE CASCADE,
    CONSTRAINT fk_message_sender
        FOREIGN KEY (sender_id) REFERENCES Users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- Table des journaux d'activité (audit des actions sensibles)
-- ==============================================================
CREATE TABLE IF NOT EXISTS ActivityLogs (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED,
    action     VARCHAR(100) NOT NULL,
    details    JSON,
    ip_address VARCHAR(45),
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_logs_user (user_id),
    KEY idx_logs_action (action),
    CONSTRAINT fk_log_user
        FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- DÉCLENCHEURS (TRIGGERS)
-- ==============================================================

DELIMITER //

-- Déclencheur : crée automatiquement un profil vide à l'inscription
CREATE TRIGGER trg_after_user_insert
AFTER INSERT ON Users
FOR EACH ROW
BEGIN
    INSERT INTO Profiles (user_id, display_name)
    VALUES (NEW.id, SUBSTRING_INDEX(NEW.email, '@', 1));
END//

-- Déclencheur : enregistre dans les journaux le bannissement d'un utilisateur
CREATE TRIGGER trg_after_user_ban
AFTER UPDATE ON Users
FOR EACH ROW
BEGIN
    IF NEW.is_banned = 1 AND OLD.is_banned = 0 THEN
        INSERT INTO ActivityLogs (user_id, action, details)
        VALUES (
            NEW.id,
            'user_banned',
            JSON_OBJECT('email', NEW.email, 'banned_at', NOW())
        );
    END IF;
END//

-- Déclencheur : interdit l'envoi d'un message si le match n'est pas encore validé
CREATE TRIGGER trg_before_message_insert
BEFORE INSERT ON Messages
FOR EACH ROW
BEGIN
    DECLARE v_is_matched TINYINT(1) DEFAULT 0;

    SELECT is_matched INTO v_is_matched
    FROM Matches
    WHERE id = NEW.match_id;

    IF v_is_matched = 0 OR v_is_matched IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Envoi impossible : le match n\'est pas encore validé.';
    END IF;
END//

-- Déclencheur : journalise la suppression définitive d'un utilisateur
CREATE TRIGGER trg_before_user_delete
BEFORE DELETE ON Users
FOR EACH ROW
BEGIN
    INSERT INTO ActivityLogs (user_id, action, details)
    VALUES (
        OLD.id,
        'user_deleted',
        JSON_OBJECT('email', OLD.email, 'role', OLD.role, 'deleted_at', NOW())
    );
END//

DELIMITER ;

-- ==============================================================
-- DONNÉES INITIALES
-- Compte administrateur par défaut
-- Mot de passe : Admin@DevMates2024
-- IMPORTANT : à changer impérativement en production
-- ==============================================================
INSERT INTO Users (email, password_hash, role) VALUES
(
    'admin@devmates.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);

SET FOREIGN_KEY_CHECKS = 1;
