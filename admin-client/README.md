# Client Admin — C# Avalonia (cross-platform)

Application de bureau dédiée à la gestion administrative de DevMates. Elle tourne sur **Windows et macOS** grâce à [Avalonia UI](https://avaloniaui.net/). Elle communique exclusivement avec l'API REST — aucune connexion directe à MySQL n'est effectuée.

## Prérequis

- Windows 10+ ou macOS 11+
- [.NET 8 SDK](https://dotnet.microsoft.com/download/dotnet/8.0)
- Le backend Docker doit être démarré et accessible sur `http://localhost:8080`

## Structure du projet

```
admin-client/DevMatesAdmin/
├── DevMatesAdmin.csproj     ← Projet .NET 8 Avalonia (net8.0 — cross-platform)
├── Program.cs               ← Point d'entrée Avalonia
├── App.axaml                ← Définition de l'application + thème Fluent
├── App.axaml.cs             ← Démarrage : instancie ApiService et LoginWindow
├── Models/
│   └── User.cs              ← Modèles (User, LoginResponse, ApiErreur)
├── Services/
│   └── ApiService.cs        ← Communications HTTP avec l'API via HttpClient
├── Helpers/
│   └── DialogHelper.cs      ← Boîtes de dialogue modales (info / confirmation)
└── Views/
    ├── LoginWindow.axaml     ← Fenêtre de connexion (XAML)
    ├── LoginWindow.axaml.cs  ← Code-behind connexion
    ├── MainWindow.axaml      ← Tableau de bord (DataGrid + boutons d'action)
    └── MainWindow.axaml.cs   ← Code-behind tableau de bord
```

## Compilation et exécution

```bash
cd admin-client/DevMatesAdmin

# Restauration des dépendances NuGet
dotnet restore

# Compilation et lancement
dotnet run

# Publier un exécutable autonome Windows
dotnet publish -c Release -r win-x64 --self-contained -o publish/windows

# Publier un exécutable autonome macOS (Apple Silicon)
dotnet publish -c Release -r osx-arm64 --self-contained -o publish/macos-arm64

# Publier un exécutable autonome macOS (Intel)
dotnet publish -c Release -r osx-x64 --self-contained -o publish/macos-x64
```

## Fonctionnement

### Connexion (`LoginWindow`)

Au démarrage, `LoginWindow` demande les identifiants d'un compte `admin`. Les identifiants sont envoyés à `POST /api/auth/login`. Si le rôle retourné n'est pas `admin`, l'accès est refusé. En cas de succès, `MainWindow` s'ouvre et `LoginWindow` se ferme.

### Tableau de bord (`MainWindow`)

Affiche la liste complète des utilisateurs dans un `DataGrid` Avalonia.

| Colonne | Description |
|---|---|
| ID | Identifiant unique |
| Nom | `display_name` du profil |
| Email | Adresse email |
| Rôle | Libellé traduit en français |
| Actif | Statut d'activation du compte |
| Banni | Statut de bannissement |
| Inscription | Date de création du compte |

### Actions disponibles

| Bouton | Action | Endpoint appelé |
|---|---|---|
| Actualiser | Recharge la liste | `GET /api/admin/users` |
| Activer / Désactiver | Bascule `is_active` | `PUT /api/admin/users/:id` |
| Bannir | Passe `is_banned` à 1 | `POST /api/admin/users/:id/ban` |
| Supprimer | Suppression définitive | `DELETE /api/admin/users/:id` |

Chaque action destructive affiche une boîte de dialogue de confirmation (`DialogHelper`) avant d'être exécutée.

## Changer l'URL de l'API

Modifier la valeur dans `App.axaml.cs` :

```csharp
var apiService = new ApiService("https://api.mon-serveur.com/api");
```

## Dépendances principales

| Paquet | Version | Rôle |
|---|---|---|
| `Avalonia` | 11.2.3 | Framework UI cross-platform |
| `Avalonia.Desktop` | 11.2.3 | Support Win32 / AvaloniaNative (macOS) |
| `Avalonia.Themes.Fluent` | 11.2.3 | Thème visuel Fluent Design |
| `Avalonia.Controls.DataGrid` | 11.2.3 | Composant tableau de données |
| `Newtonsoft.Json` | 13.0.3 | Désérialisation des réponses JSON de l'API |
