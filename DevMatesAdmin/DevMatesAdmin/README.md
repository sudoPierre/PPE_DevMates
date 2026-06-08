# DevMates Admin — Guide de déploiement

Panel d'administration Windows pour la plateforme DevMates.
Application C# Windows Forms (.NET 8) — BTS SIO SLAM, épreuve E6.

---

## Packages NuGet à installer

Ouvrez un terminal dans le dossier `DevMatesAdmin/` et exécutez :

```bash
dotnet add package MySql.Data --version 8.3.0
dotnet add package BCrypt.Net-Next --version 4.0.3
```

Ou via Visual Studio : clic droit sur le projet → *Gérer les packages NuGet*.

---

## Étape 1 — Préparer la base de données

### En développement local (XAMPP)

1. Démarrer **XAMPP** → activer **Apache** et **MySQL**.
2. Ouvrir **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Importer `database.sql` (base du site PHP) si ce n'est pas déjà fait.
4. Sélectionner la base `devmates`, puis exécuter la **partie 1** de `setup_admin.sql` :

```sql
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS banni TINYINT(1) NOT NULL DEFAULT 0;
```

5. Vérifier que la table `users` contient un compte avec `role = 'admin'`
   et un mot de passe hashé en bcrypt (généré par PHP `password_hash()`).

### Sur la VM Linux (déploiement)

1. Importer `database.sql` puis exécuter `setup_admin.sql` en entier
   (y compris la partie 2 — création de l'utilisateur MySQL distant) :

```bash
mysql -u root -p devmates < database.sql
mysql -u root -p devmates < setup_admin.sql
```

2. Autoriser les connexions distantes à MariaDB/MySQL.
   Éditer le fichier de configuration (`/etc/mysql/mariadb.conf.d/50-server.cnf`
   ou `/etc/mysql/mysql.conf.d/mysqld.cnf`) :

```ini
# Changer cette ligne :
bind-address = 127.0.0.1
# Par :
bind-address = 0.0.0.0
```

3. Redémarrer le service :

```bash
sudo systemctl restart mariadb   # ou : sudo systemctl restart mysql
```

4. Ouvrir le port 3306 dans le pare-feu si nécessaire :

```bash
sudo ufw allow 3306/tcp
sudo ufw reload
```

---

## Étape 2 — Gérer l'IP en DHCP (important avant chaque démo)

La VM est configurée en DHCP, son IP peut changer à chaque redémarrage.

**Avant chaque démo :**

1. Se connecter à la VM et afficher son IP :

```bash
ip a
# Chercher la ligne "inet" sur l'interface réseau (eth0, ens18, etc.)
# Exemple : inet 192.168.1.42/24
```

2. Reporter cette IP dans **`Config.cs`** :
   - Commenter la ligne locale
   - Décommenter la ligne distante et remplacer `IP_DE_LA_VM`

```csharp
// --- Connexion LOCALE --- (mettre en commentaire)
// public const string ConnexionString = "Server=localhost;...";

// --- Connexion DISTANTE --- (activer cette ligne)
public const string ConnexionString =
    "Server=192.168.1.42;Port=3306;Database=devmates;Uid=admin_csharp;Pwd=MOT_DE_PASSE;";
```

3. Recompiler l'application (voir Étape 3).

**Alternative recommandée :** configurer une IP statique sur la VM pour éviter
cette manipulation. Demander à l'administrateur réseau ou configurer dans
`/etc/network/interfaces` ou via Netplan (`/etc/netplan/*.yaml`).

---

## Étape 3 — Compiler en exécutable Windows

Dans le dossier `DevMatesAdmin/`, exécuter :

```bash
dotnet publish -c Release -r win-x64 --self-contained true
```

L'exécutable se trouve dans :

```
DevMatesAdmin/bin/Release/net8.0-windows/win-x64/publish/DevMatesAdmin.exe
```

> **--self-contained true** : inclut le runtime .NET dans le dossier `publish/`.
> L'application fonctionne sur n'importe quel PC Windows 64 bits sans installer .NET.

Pour distribuer : copier l'**intégralité** du dossier `publish/` sur le poste cible.

---

## Étape 4 — Lancer l'application

1. Connecter le poste Windows au même réseau que la VM.
2. Double-cliquer sur `DevMatesAdmin.exe` dans le dossier `publish/`.
3. Entrer les identifiants de l'administrateur (email + mot de passe).

---

## Sauvegarde de la base de données

L'épreuve E6 évalue que la base est sauvegardée selon une planification.

**Commande manuelle (export complet) :**

```bash
# Sur la VM Linux :
mysqldump -u root -p devmates > sauvegarde_devmates_$(date +%Y%m%d).sql
```

**Planification automatique (cron) — exemple : sauvegarde chaque nuit à 2h00 :**

```bash
crontab -e
# Ajouter cette ligne :
0 2 * * * mysqldump -u root -pMOT_DE_PASSE devmates > /var/backups/devmates_$(date +\%Y\%m\%d).sql
```

**Restaurer une sauvegarde :**

```bash
mysql -u root -p devmates < sauvegarde_devmates_20241201.sql
```

> Conserver les sauvegardes dans un emplacement séparé de la VM
> (NAS, clé USB, dossier partagé) pour se prémunir d'une panne matérielle.

---

## Résumé de l'arborescence

```
DevMatesAdmin/
├── Config.cs                  → Chaînes de connexion (1 ligne à changer)
├── Program.cs                 → Point d'entrée
├── DevMatesAdmin.csproj       → Projet + packages NuGet
├── setup_admin.sql            → Script SQL de préparation
├── Models/
│   ├── Database.cs            → Connexion MySQL centralisée
│   ├── User.cs                → Modèle utilisateur
│   └── UserRepository.cs      → Requêtes SQL (CRUD)
├── Controllers/
│   ├── AuthController.cs      → Connexion admin + session
│   └── UserController.cs      → Logique métier utilisateurs
└── Views/
    ├── LoginForm.cs           → Fenêtre de connexion
    ├── MainForm.cs            → Fenêtre principale (tableau)
    └── EditUserForm.cs        → Fenêtre de modification
```
