// =============================================
// FICHIER : Config.cs
// RÔLE    : Centralise la configuration de connexion
//           à la base de données MySQL/MariaDB.
//           UNE SEULE LIGNE à modifier pour basculer
//           entre développement local et déploiement VM.
// =============================================

namespace DevMatesAdmin
{
    public static class Config
    {
        // --- Connexion LOCALE (développement avec XAMPP) ---
        // Mot de passe vide = configuration XAMPP par défaut
        public const string ConnexionString =
            "Server=localhost;Port=3306;Database=devmates;Uid=root;Pwd=;";

        // --- Connexion DISTANTE (déploiement sur la VM) ---
        // ⚠️ La VM est en DHCP : avant la démo, faire "ip a" sur la VM pour lire
        //    l'IP du jour, puis la coller ci-dessous et activer UNIQUEMENT cette ligne.
        //    (Mettre l'autre en commentaire)
        // public const string ConnexionString =
        //     "Server=IP_DE_LA_VM;Port=3306;Database=devmates;Uid=admin_csharp;Pwd=MOT_DE_PASSE;";
    }
}
