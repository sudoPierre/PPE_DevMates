# Client Web — React

Application web monopage (SPA) consommant l'API REST DevMates.

## Stack technique

| Outil | Version | Rôle |
|---|---|---|
| React | 18.3 | Bibliothèque UI |
| React Router | 6.x | Routage côté client |
| Axios | 1.6 | Requêtes HTTP vers l'API |
| Vite | 5.x | Bundler et serveur de développement |
| Nginx | 1.25 | Serveur de fichiers statiques (production) |

## Structure des fichiers

```
frontend/
├── Dockerfile          ← Build multi-stage Node → Nginx
├── nginx.conf          ← Config Nginx : SPA + proxy /api/ → backend
├── index.html          ← Point d'entrée HTML (racine Vite)
├── package.json
├── vite.config.js
└── src/
    ├── main.jsx            ← Montage React dans #root
    ├── App.jsx             ← Routeur et garde d'accès (RoutePrivee)
    ├── index.css           ← Styles globaux + variables CSS
    ├── api/
    │   └── api.js          ← Instance Axios partagée + intercepteurs JWT
    ├── context/
    │   └── AuthContext.jsx ← Contexte global d'authentification
    └── pages/
        ├── Login.jsx       ← Formulaire de connexion
        ├── Register.jsx    ← Formulaire d'inscription
        ├── Profile.jsx     ← Édition du profil utilisateur
        ├── Matching.jsx    ← Vue principale de swipe + liste des matchs
        └── Chat.jsx        ← Messagerie avec Short Polling
```

## Vues de l'application

### Connexion et inscription (`/login`, `/register`)

- Formulaires avec gestion des erreurs API.
- À l'inscription, le rôle est choisi (`developpeur` ou `porteur_projet`).
- Après connexion, le token JWT et les infos utilisateur sont stockés dans le `localStorage`.
- Redirection automatique vers `/match` si déjà connecté.

### Profil (`/profile`)

- Affichage et édition du profil : nom, biographie, compétences, liens GitHub/LinkedIn.
- Les compétences sont gérées sous forme de tags (ajout par la touche Entrée, suppression au clic).
- Sauvegarde via `PUT /api/profile`.

### Matching (`/match`)

- Affiche les profils candidats un par un (chargés par lot de 10 depuis `/api/match/candidates`).
- Bouton ♥ → like, bouton ✕ → dislike (`POST /api/match/action`).
- Détection et affichage d'un match mutuel avec proposition d'ouvrir le chat.
- Liste des matchs validés en bas de page, cliquables pour accéder à la conversation.

### Chat (`/chat/:matchId`)

- Historique des messages chargé à l'ouverture.
- **Short Polling** : toutes les **4 secondes**, le client interroge `GET /api/messages/:matchId?since=<dernierHorodatage>` pour récupérer uniquement les nouveaux messages.
- Défilement automatique vers le dernier message.
- Envoi par le bouton ou la touche Entrée.

## Authentification et sécurité

Le fichier `src/api/api.js` configure une instance Axios partagée qui :

1. Injecte automatiquement le token JWT dans l'en-tête `Authorization: Bearer <token>` de chaque requête.
2. Intercepte les réponses `401` pour nettoyer le `localStorage` et rediriger vers `/login`.

La composante `RoutePrivee` dans `App.jsx` protège toutes les routes nécessitant une session active.

## Développement local (sans Docker)

```bash
cd frontend
npm install
npm run dev
# Application disponible sur http://localhost:5173
```

Le fichier `vite.config.js` configure un proxy : toutes les requêtes vers `/api` sont redirigées vers `http://localhost:8080` (backend PHP local ou Docker).

## Construction pour la production

Le `Dockerfile` utilise un build **multi-stage** :

1. **Étape `builder`** : image `node:20-alpine` — installe les dépendances et compile via `npm run build`. Les fichiers produits sont dans `/app/dist`.
2. **Étape finale** : image `nginx:1.25-alpine` — copie le dossier `dist/` et sert les fichiers statiques avec la configuration `nginx.conf`.

La configuration Nginx gère deux cas :
- Les routes React (`/match`, `/profile`…) sont renvoyées vers `index.html` pour que React Router prenne en charge la navigation.
- Les appels `/api/` sont proxifiés vers le service Docker `backend:80`.
