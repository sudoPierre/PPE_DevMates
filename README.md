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
Démarrer Apache et MySQL depuis le panneau XAMPP.
Créer la base de données en important database.sql via phpMyAdmin ou en exécutant :

http://localhost/devmates/setup.php
Accéder à l'application :

http://localhost/devmates
Comptes de test
Email	Mot de passe	Rôle
alice@devmates.com	devmates123	user
bob@devmates.com	devmates123	user
clara@devmates.com	devmates123	user
admin@devmates.com	devmates123	admin
Structure du projet

devmates/
├── config/         Configuration BDD
├── controllers/    Logique métier (Auth, Match, Message, Profile)
├── models/         Accès aux données (User, Message)
├── views/          Templates HTML
├── css/            Styles
├── index.php       Point d'entrée (front controller)
├── setup.php       Initialisation de la BDD
└── database.sql    Schéma SQL
Auteur
Lina Karouche
