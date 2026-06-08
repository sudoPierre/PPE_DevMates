-- =============================================
-- FICHIER : setup_admin.sql
-- RÔLE    : Script SQL à exécuter UNE SEULE FOIS
--           pour préparer la base devmates à
--           l'utilisation par cette application C#.
--
-- ÉTAPES :
--   1. Importer d'abord database.sql (site PHP)
--   2. Exécuter CE script
-- =============================================

-- -----------------------------------------------
-- PARTIE 1 : LOCALE + DISTANTE
-- Ajoute la colonne "banni" à la table users.
-- (À exécuter sur XAMPP en dev ET sur la VM au déploiement)
-- -----------------------------------------------

-- Si la colonne n'existe pas encore, l'ajouter
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS banni TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '0 = actif, 1 = banni';


-- -----------------------------------------------
-- PARTIE 2 : DISTANTE UNIQUEMENT (VM Linux / Proxmox)
-- Crée un utilisateur MySQL autorisé à se connecter
-- depuis n'importe quelle IP (nécessaire car C# tourne
-- sur un poste Windows, pas sur la VM elle-même).
--
-- ⚠️ Remplacez MOT_DE_PASSE par un vrai mot de passe
--    puis reportez-le dans Config.cs (ligne distante).
-- ⚠️ N'exécuter ces 3 lignes QUE sur la VM, pas en local.
-- -----------------------------------------------

-- CREATE USER 'admin_csharp'@'%' IDENTIFIED BY 'MOT_DE_PASSE';
-- GRANT ALL PRIVILEGES ON devmates.* TO 'admin_csharp'@'%';
-- FLUSH PRIVILEGES;


-- -----------------------------------------------
-- VÉRIFICATION : voir la structure de users
-- -----------------------------------------------
-- DESCRIBE users;
