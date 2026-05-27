# Base de données — DevMates

Schéma MySQL 8.0, initialisé automatiquement au démarrage du conteneur Docker.

## Tables

### `Users`

Stocke les comptes utilisateurs. Trois rôles possibles.

| Colonne | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Clé primaire auto-incrémentée |
| `email` | VARCHAR(255) | Adresse email unique |
| `password_hash` | VARCHAR(255) | Hash bcrypt (coût 12) |
| `role` | ENUM | `admin`, `developpeur`, `porteur_projet` |
| `is_active` | TINYINT(1) | Compte actif (1) ou désactivé (0) |
| `is_banned` | TINYINT(1) | Compte banni (1) ou non (0) |
| `created_at` | DATETIME | Date de création |
| `updated_at` | DATETIME | Dernière modification |

---

### `Profiles`

Informations publiques liées à un utilisateur. Créé automatiquement à l'inscription via un trigger.

| Colonne | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Clé primaire |
| `user_id` | INT UNSIGNED | Clé étrangère → `Users.id` (CASCADE) |
| `display_name` | VARCHAR(100) | Nom affiché (initialisé avec le préfixe email) |
| `bio` | TEXT | Présentation libre |
| `skills` | JSON | Tableau de compétences. Ex : `["PHP", "React"]` |
| `project_ideas` | JSON | Tableau d'idées de projets |
| `github_url` | VARCHAR(255) | Lien profil GitHub |
| `linkedin_url` | VARCHAR(255) | Lien profil LinkedIn |
| `avatar_url` | VARCHAR(255) | URL de l'avatar |

---

### `Matches`

Enregistre les interactions de swipe entre deux utilisateurs.

| Colonne | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Clé primaire |
| `user_id_1` | INT UNSIGNED | Premier utilisateur à avoir swipé |
| `user_id_2` | INT UNSIGNED | Cible du swipe |
| `action_user_1` | ENUM | `like`, `dislike`, `pending` |
| `action_user_2` | ENUM | `like`, `dislike`, `pending` |
| `is_matched` | TINYINT(1) | 1 si les deux ont liké (match validé) |
| `matched_at` | DATETIME | Horodatage du match mutuel |

> La paire `(user_id_1, user_id_2)` est unique — un seul enregistrement par duo.  
> `is_matched` est mis à `1` par le contrôleur PHP (`MatchController`) dès que les deux actions sont `like`.

---

### `Messages`

Messages échangés dans une conversation. Requiert un match validé (`is_matched = 1`).

| Colonne | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Clé primaire |
| `match_id` | INT UNSIGNED | Clé étrangère → `Matches.id` (CASCADE) |
| `sender_id` | INT UNSIGNED | Clé étrangère → `Users.id` |
| `content` | TEXT | Corps du message |
| `is_read` | TINYINT(1) | Lu (1) ou non lu (0) |
| `sent_at` | DATETIME | Horodatage d'envoi |

---

### `ActivityLogs`

Journal d'audit des actions sensibles (bannissements, suppressions).

| Colonne | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Clé primaire |
| `user_id` | INT UNSIGNED | Utilisateur concerné (NULL si supprimé) |
| `action` | VARCHAR(100) | Libellé de l'action (`user_banned`, `user_deleted`) |
| `details` | JSON | Données contextuelles (email, date…) |
| `ip_address` | VARCHAR(45) | Adresse IP de l'auteur de l'action |
| `created_at` | DATETIME | Horodatage |

---

## Triggers

| Nom | Événement | Effet |
|---|---|---|
| `trg_after_user_insert` | AFTER INSERT sur `Users` | Crée automatiquement un profil vide avec le préfixe email comme `display_name` |
| `trg_after_user_ban` | AFTER UPDATE sur `Users` | Insère une entrée dans `ActivityLogs` quand `is_banned` passe de 0 à 1 |
| `trg_before_message_insert` | BEFORE INSERT sur `Messages` | Bloque l'insertion si le match référencé n'est pas encore validé (`is_matched = 0`) |
| `trg_before_user_delete` | BEFORE DELETE sur `Users` | Journalise la suppression avant qu'elle soit effective |

---

## Initialisation Docker

Le fichier `devmates_db.sql` est monté dans `/docker-entrypoint-initdb.d/` du conteneur MySQL. Il est exécuté automatiquement à la **première création** du volume Docker.

Pour forcer une réinitialisation complète :

```bash
docker-compose down -v   # supprime le volume de données
docker-compose up --build
```

## Connexion directe (développement)

```bash
# Depuis l'hôte (MySQL exposé sur le port 3306)
mysql -h 127.0.0.1 -P 3306 -u devmates_user -p devmates
```
