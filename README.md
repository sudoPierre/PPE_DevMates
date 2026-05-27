# DevMates

Plateforme de mise en relation entre **développeurs** et **porteurs de projets**, sur le modèle du swipe Tinder.

## Présentation

DevMates permet à des développeurs cherchant un projet, et à des porteurs de projets cherchant un associé technique, de se découvrir mutuellement, de s'apprécier et d'échanger via une messagerie intégrée.

## Architecture globale

```
┌─────────────────────────────────────────────────────────┐
│                    Docker Compose                        │
│                                                          │
│  ┌──────────────┐   ┌──────────────┐   ┌─────────────┐ │
│  │  MySQL 8.0   │   │  PHP/Apache  │   │ React/Nginx │ │
│  │  :3306       │◄──│  :8080       │◄──│  :3000      │ │
│  └──────────────┘   └──────────────┘   └─────────────┘ │
└─────────────────────────────────────────────────────────┘
          ▲
          │  HttpClient (REST)
          │
┌─────────────────┐
│  C# WinForms    │  (exécuté localement sous Windows)
│  Client Admin   │
└─────────────────┘
```

| Composant | Technologie | Port |
|---|---|---|
| Base de données | MySQL 8.0 | 3306 |
| API REST | PHP 8.2 + Apache | 8080 |
| Client web | React 18 + Nginx | 3000 |
| Client admin | C# .NET 8 WinForms | — |

## Prérequis

- [Docker](https://www.docker.com/) >= 24 et Docker Compose >= 2
- [.NET 8 SDK](https://dotnet.microsoft.com/) (uniquement pour le client admin Windows)

## Démarrage rapide

```bash
# 1. Cloner le dépôt
git clone https://github.com/sudoPierre/PPE_DevMates.git
cd PPE_DevMates

# 2. Configurer les variables d'environnement
cp .env.example .env
# Éditer .env et changer les mots de passe

# 3. Construire et démarrer tous les conteneurs
docker-compose up --build

# 4. Accéder à l'application
#    Client web  → http://localhost:3000
#    API backend → http://localhost:8080/api
```

## Identifiants par défaut

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `admin@devmates.com` | `password` |

> **Important** : changer ce mot de passe avant tout déploiement.  
> Générer un nouveau hash avec : `php -r "echo password_hash('NouveauMotDePasse', PASSWORD_BCRYPT, ['cost'=>12]);"`  
> Puis mettre à jour le hash dans `database/devmates_db.sql`.

## Documentation par composant

| Composant | Documentation |
|---|---|
| Base de données | [database/README.md](database/README.md) |
| API PHP Backend | [backend/README.md](backend/README.md) |
| Client web React | [frontend/README.md](frontend/README.md) |
| Client admin C# | [admin-client/README.md](admin-client/README.md) |

## Contraintes du projet

- Tous les commentaires, messages utilisateur et commits sont rédigés en **français**.
- L'authentification est gérée par des **tokens JWT** (HS256, expiration 24h).
- La messagerie utilise le **Short Polling** (pas de WebSocket) toutes les 4 secondes.
- Le client admin C# communique **uniquement via l'API REST** — aucune connexion directe à MySQL.
