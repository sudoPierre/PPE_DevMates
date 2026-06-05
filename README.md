# DevMates

Plateforme de mise en relation entre développeurs, basée sur la compatibilité de compétences.

## Fonctionnalités

- Inscription / connexion sécurisée
- Profils développeurs (bio, compétences, rôle)
- Algorithme de matching basé sur les compétences communes
- Messagerie entre utilisateurs
- Pages statiques (FAQ, mentions légales)
- Panel administrateur

## Stack technique

- **Backend :** PHP (PDO, architecture MVC)
- **Base de données :** MySQL
- **Frontend :** HTML, CSS, Bootstrap
- **Serveur local :** XAMPP

## Installation

### Prérequis

- XAMPP (Apache + MySQL)
- PHP 7.4+

### Étapes

1. Cloner le dépôt dans `xampp/htdocs/` :
   ```bash
   git clone <url-du-repo> devmates
   ```
2. Démarrer Apache et MySQL depuis le panneau XAMPP.
3. Créer la base de données en important `database.sql` via phpMyAdmin ou en exécutant :
   ```
   http://localhost/devmates/setup.php
   ```
4. Accéder à l'application :
   ```
   http://localhost/devmates
   ```

## Comptes de test

- **alice@devmates.com** — mot de passe : `devmates123` (utilisateur)
- **bob@devmates.com** — mot de passe : `devmates123` (utilisateur)
- **clara@devmates.com** — mot de passe : `devmates123` (utilisateur)
- **admin@devmates.com** — mot de passe : `devmates123` (administrateur)

## Structure du projet

```
devmates/
├── config/         Configuration BDD
├── controllers/    Logique métier (Auth, Match, Message, Profile)
├── models/         Accès aux données (User, Message)
├── views/          Templates HTML
├── css/            Styles
├── index.php       Point d'entrée (front controller)
├── setup.php       Initialisation de la BDD
└── database.sql    Schéma SQL
```

## Auteur

Lina Karouche
