# Backend — API REST PHP

API REST en PHP 8.2 orientée objet, servie par Apache, sans framework ni Composer.

## Structure des fichiers

```
backend/
├── Dockerfile
├── apache/
│   └── devmates.conf       ← VirtualHost Apache
└── src/
    ├── index.php            ← Front controller + autoloader + déclaration des routes
    ├── .htaccess            ← Rewrite vers index.php + en-têtes CORS
    ├── config/
    │   └── Database.php     ← Connexion PDO (Singleton)
    ├── router/
    │   └── Router.php       ← Routeur HTTP avec support des paramètres /:id
    ├── middleware/
    │   └── AuthMiddleware.php ← Génération et vérification JWT HS256
    └── controllers/
        ├── AuthController.php
        ├── ProfileController.php
        ├── MatchController.php
        ├── MessageController.php
        └── AdminController.php
```

## Démarrage

Le backend démarre automatiquement via Docker Compose. Il attend que MySQL soit sain (`healthcheck`) avant de démarrer.

```bash
docker-compose up backend
```

L'API est accessible sur `http://localhost:8080`.

## Variables d'environnement

| Variable | Description | Valeur par défaut |
|---|---|---|
| `DB_HOST` | Hôte MySQL (nom du service Docker) | `db` |
| `DB_PORT` | Port MySQL | `3306` |
| `DB_NAME` | Nom de la base de données | `devmates` |
| `DB_USER` | Utilisateur MySQL | `devmates_user` |
| `DB_PASSWORD` | Mot de passe MySQL | — |
| `JWT_SECRET` | Clé secrète de signature des tokens | — |

## Endpoints REST

### Authentification

| Méthode | Route | Auth | Description |
|---|---|---|---|
| POST | `/api/auth/register` | Non | Inscription — retourne un token JWT |
| POST | `/api/auth/login` | Non | Connexion — retourne un token JWT |

**Corps de `/api/auth/register` :**
```json
{
  "email": "alice@exemple.com",
  "password": "motdepasse123",
  "role": "developpeur"
}
```

**Corps de `/api/auth/login` :**
```json
{
  "email": "alice@exemple.com",
  "password": "motdepasse123"
}
```

**Réponse (les deux endpoints) :**
```json
{
  "message": "Connexion réussie.",
  "token": "<jwt>",
  "user": { "id": 1, "email": "alice@exemple.com", "role": "developpeur" }
}
```

---

### Profils

Toutes ces routes requièrent le token JWT dans l'en-tête `Authorization: Bearer <token>`.

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/profile` | Profil complet de l'utilisateur connecté |
| PUT | `/api/profile` | Mise à jour du profil connecté |
| GET | `/api/profile/:id` | Profil public d'un autre utilisateur |

**Corps de `PUT /api/profile` (tous les champs sont optionnels) :**
```json
{
  "display_name": "Alice Dupont",
  "bio": "Développeuse full-stack passionnée.",
  "skills": ["PHP", "React", "Docker"],
  "project_ideas": ["Application de covoiturage"],
  "github_url": "https://github.com/alice",
  "linkedin_url": "https://linkedin.com/in/alice"
}
```

---

### Matching

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/match/candidates` | 10 profils non encore vus (ordre aléatoire) |
| POST | `/api/match/action` | Enregistre un like ou dislike |
| GET | `/api/match` | Liste des matchs validés de l'utilisateur |

**Corps de `POST /api/match/action` :**
```json
{
  "target_id": 5,
  "action": "like"
}
```

**Réponse (match mutuel détecté) :**
```json
{
  "message": "Action enregistrée.",
  "is_matched": true,
  "match_id": 12
}
```

---

### Messagerie (Short Polling)

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/messages/:matchId` | Historique complet de la conversation |
| GET | `/api/messages/:matchId?since=2024-01-01 12:00:00` | Messages postérieurs à la date donnée |
| POST | `/api/messages/:matchId` | Envoyer un message |

Le paramètre `since` est utilisé par le client pour le **Short Polling** : il ne demande que les nouveaux messages apparus depuis le dernier chargement, réduisant la charge des échanges.

**Corps de `POST /api/messages/:matchId` :**
```json
{ "content": "Bonjour ! Votre profil m'intéresse." }
```

---

### Administration (rôle `admin` requis)

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/admin/users` | Liste tous les utilisateurs |
| PUT | `/api/admin/users/:id` | Modifie `role`, `is_active` ou `is_banned` |
| DELETE | `/api/admin/users/:id` | Supprime définitivement un utilisateur |
| POST | `/api/admin/users/:id/ban` | Bannit un utilisateur |

---

## Authentification JWT

Chaque requête protégée doit inclure l'en-tête :

```
Authorization: Bearer <token>
```

Le token JWT est signé en **HS256** avec la clé définie dans `JWT_SECRET`. Il contient :

```json
{
  "sub": 1,
  "email": "alice@exemple.com",
  "role": "developpeur",
  "iat": 1700000000,
  "exp": 1700086400
}
```

- Durée de validité : **24 heures**
- En cas d'expiration, l'API retourne `401 Unauthorized`

## Gestion des erreurs

Toutes les réponses d'erreur suivent le format :

```json
{ "erreur": "Description lisible de l'erreur." }
```

| Code HTTP | Signification |
|---|---|
| 400 | Données de la requête invalides |
| 401 | Token manquant, invalide ou expiré |
| 403 | Accès refusé (rôle insuffisant ou compte banni) |
| 404 | Ressource introuvable |
| 409 | Conflit (ex : email déjà utilisé) |
| 503 | Base de données inaccessible |

## Architecture interne

Le routeur supporte les paramètres dynamiques via la syntaxe `/:param`, convertis en expressions régulières (`([^/]+)`). L'autoloader PSR-4 maison mappe les espaces de noms PHP vers les chemins de fichiers sans dépendance à Composer.
