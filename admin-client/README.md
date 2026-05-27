# Client Admin — C# WinForms

Application de bureau Windows dédiée à la gestion administrative de la plateforme DevMates. Elle communique exclusivement avec l'API REST — aucune connexion directe à MySQL n'est effectuée.

## Prérequis

- Windows 10 ou supérieur
- [.NET 8 SDK](https://dotnet.microsoft.com/download/dotnet/8.0) (ou Runtime si vous utilisez un exécutable publié)
- Le backend Docker doit être démarré et accessible sur `http://localhost:8080`

## Structure du projet

```
admin-client/DevMatesAdmin/
├── DevMatesAdmin.csproj     ← Projet .NET 8 WinForms
├── Program.cs               ← Point d'entrée : LoginForm → MainForm
├── Models/
│   └── User.cs              ← Modèles de données (User, LoginResponse, ApiErreur)
├── Services/
│   └── ApiService.cs        ← Toutes les communications HTTP avec l'API
└── Forms/
    ├── LoginForm.cs         ← Formulaire de connexion admin
    └── MainForm.cs          ← Tableau de bord principal
```

## Compilation et exécution

```bash
cd admin-client/DevMatesAdmin

# Restauration des dépendances NuGet
dotnet restore

# Compilation et lancement
dotnet run

# Ou : publier un exécutable autonome Windows
dotnet publish -c Release -r win-x64 --self-contained
```

L'exécutable produit se trouve dans `bin/Release/net8.0-windows/win-x64/publish/`.

## Fonctionnement

### Connexion

Au démarrage, `LoginForm` demande les identifiants d'un compte ayant le rôle `admin`. Les identifiants sont envoyés à `POST /api/auth/login`. Si le rôle retourné n'est pas `admin`, l'accès est refusé même si les identifiants sont corrects.

### Tableau de bord (`MainForm`)

La fenêtre principale affiche la liste complète des utilisateurs dans un `DataGridView`.

| Colonne | Description |
|---|---|
| ID | Identifiant unique |
| Nom | `display_name` du profil |
| Email | Adresse email |
| Rôle | Libellé traduit en français |
| Actif | Statut d'activation du compte |
| Banni | Statut de bannissement |
| Inscription | Date de création du compte |

Les lignes des comptes bannis sont colorées en rouge clair pour une identification visuelle rapide.

### Actions disponibles

| Bouton | Action | Endpoint appelé |
|---|---|---|
| Actualiser | Recharge la liste | `GET /api/admin/users` |
| Activer / Désactiver | Bascule `is_active` | `PUT /api/admin/users/:id` |
| Bannir | Passe `is_banned` à 1 | `POST /api/admin/users/:id/ban` |
| Supprimer | Suppression définitive | `DELETE /api/admin/users/:id` |

Chaque action destructive (bannissement, suppression) affiche une boîte de dialogue de confirmation avant d'être exécutée.

## Architecture du service API

`ApiService` utilise une instance statique partagée de `HttpClient` (bonne pratique .NET pour éviter l'épuisement des sockets). Après connexion réussie, le token JWT est défini dans `DefaultRequestHeaders.Authorization` et sera automatiquement envoyé dans toutes les requêtes suivantes.

```csharp
// Exemple d'utilisation dans un formulaire
var apiService = new ApiService("http://localhost:8080/api");
var reponse = await apiService.ConnecterAsync("admin@devmates.com", "motdepasse");
apiService.SetToken(reponse.Token);
var utilisateurs = await apiService.ObtenirUtilisateursAsync();
```

## Changer l'URL de l'API

Si le backend est déployé sur un serveur distant, modifier l'URL dans `Program.cs` :

```csharp
var apiService = new ApiService("https://api.mon-serveur.com/api");
```
