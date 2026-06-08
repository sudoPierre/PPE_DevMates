// =============================================
// FICHIER : Models/Database.cs
// RÔLE    : Fournit une connexion MySQL centralisée.
//           Tous les Repository l'utilisent via
//           Database.GetConnection().
//           La chaîne de connexion vient de Config.cs.
// =============================================

using System;
using System.Windows.Forms;
using MySql.Data.MySqlClient;

namespace DevMatesAdmin.Models
{
    public static class Database
    {
        /// <summary>
        /// Ouvre et retourne une connexion MySQL.
        /// Retourne null et affiche un MessageBox explicite si la connexion échoue.
        /// → Vérifiez que XAMPP est démarré (en local) ou que l'IP est correcte (VM).
        /// </summary>
        public static MySqlConnection GetConnection()
        {
            try
            {
                var connexion = new MySqlConnection(Config.ConnexionString);
                connexion.Open();
                return connexion;
            }
            catch (Exception ex)
            {
                MessageBox.Show(
                    "Impossible de se connecter à la base de données.\n\n" +
                    "Vérifiez les points suivants :\n" +
                    "  • En développement local : XAMPP est-il démarré ? Le service MySQL est-il actif ?\n" +
                    "  • En déploiement VM : l'IP dans Config.cs est-elle correcte ? (faire \"ip a\" sur la VM)\n" +
                    "  • Le nom de la base \"devmates\" existe-t-il ?\n\n" +
                    "Détail technique : " + ex.Message,
                    "Erreur de connexion à la base",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error);
                return null;
            }
        }
    }
}
