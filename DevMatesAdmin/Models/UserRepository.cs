// =============================================
// FICHIER : Models/UserRepository.cs
// RÔLE    : Toutes les requêtes SQL sur la table
//           users (lister, modifier, supprimer, bannir).
//
// IMPORTANT : Toutes les requêtes sont PARAMÉTRÉES
//             (syntaxe @parametre) pour éviter les
//             injections SQL — exigence de sécurité.
//
// ⚠️ Vérifiez que les noms de colonnes correspondent
//    à votre base (ex: "mot_de_passe", "date_inscription").
// =============================================

using System;
using System.Collections.Generic;
using MySql.Data.MySqlClient;

namespace DevMatesAdmin.Models
{
    public class UserRepository
    {
        /// <summary>
        /// Récupère tous les utilisateurs de la base.
        /// Utilisé pour remplir le DataGridView de la fenêtre principale.
        /// </summary>
        public List<User> GetAll()
        {
            var liste = new List<User>();

            using (var conn = Database.GetConnection())
            {
                if (conn == null) return liste; // Échec de connexion, liste vide

                string sql = @"SELECT id, nom, prenom, email, role,
                                      competences, bio, date_inscription, banni
                               FROM users
                               ORDER BY id ASC";

                using (var cmd = new MySqlCommand(sql, conn))
                using (var reader = cmd.ExecuteReader())
                {
                    while (reader.Read())
                    {
                        liste.Add(new User
                        {
                            Id              = reader.GetInt32("id"),
                            Nom             = reader.IsDBNull(reader.GetOrdinal("nom"))             ? "" : reader.GetString("nom"),
                            Prenom          = reader.IsDBNull(reader.GetOrdinal("prenom"))          ? "" : reader.GetString("prenom"),
                            Email           = reader.GetString("email"),
                            Role            = reader.GetString("role"),
                            Competences     = reader.IsDBNull(reader.GetOrdinal("competences"))     ? "" : reader.GetString("competences"),
                            Bio             = reader.IsDBNull(reader.GetOrdinal("bio"))             ? "" : reader.GetString("bio"),
                            DateInscription = reader.IsDBNull(reader.GetOrdinal("date_inscription")) ? DateTime.MinValue : reader.GetDateTime("date_inscription"),
                            // Convert.ToBoolean fonctionne avec TINYINT(1) retourné par MySQL
                            Banni           = Convert.ToBoolean(reader["banni"])
                        });
                    }
                }
            }

            return liste;
        }

        /// <summary>
        /// Récupère un utilisateur par son identifiant.
        /// Utilisé pour pré-remplir le formulaire de modification.
        /// </summary>
        public User GetById(int id)
        {
            using (var conn = Database.GetConnection())
            {
                if (conn == null) return null;

                string sql = @"SELECT id, nom, prenom, email, mot_de_passe,
                                      role, competences, bio, date_inscription, banni
                               FROM users WHERE id = @id";

                using (var cmd = new MySqlCommand(sql, conn))
                {
                    cmd.Parameters.AddWithValue("@id", id); // Paramètre anti-injection

                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            return new User
                            {
                                Id              = reader.GetInt32("id"),
                                Nom             = reader.IsDBNull(reader.GetOrdinal("nom"))             ? "" : reader.GetString("nom"),
                                Prenom          = reader.IsDBNull(reader.GetOrdinal("prenom"))          ? "" : reader.GetString("prenom"),
                                Email           = reader.GetString("email"),
                                MotDePasse      = reader.GetString("mot_de_passe"), // Hash bcrypt
                                Role            = reader.GetString("role"),
                                Competences     = reader.IsDBNull(reader.GetOrdinal("competences"))     ? "" : reader.GetString("competences"),
                                Bio             = reader.IsDBNull(reader.GetOrdinal("bio"))             ? "" : reader.GetString("bio"),
                                DateInscription = reader.IsDBNull(reader.GetOrdinal("date_inscription")) ? DateTime.MinValue : reader.GetDateTime("date_inscription"),
                                Banni           = Convert.ToBoolean(reader["banni"])
                            };
                        }
                    }
                }
            }
            return null;
        }

        /// <summary>
        /// Recherche un utilisateur par son adresse email.
        /// Utilisé par AuthController lors de la connexion admin.
        /// </summary>
        public User GetByEmail(string email)
        {
            using (var conn = Database.GetConnection())
            {
                if (conn == null) return null;

                string sql = "SELECT id, nom, prenom, email, mot_de_passe, role FROM users WHERE email = @email";

                using (var cmd = new MySqlCommand(sql, conn))
                {
                    cmd.Parameters.AddWithValue("@email", email);

                    using (var reader = cmd.ExecuteReader())
                    {
                        if (reader.Read())
                        {
                            return new User
                            {
                                Id         = reader.GetInt32("id"),
                                Nom        = reader.IsDBNull(reader.GetOrdinal("nom"))    ? "" : reader.GetString("nom"),
                                Prenom     = reader.IsDBNull(reader.GetOrdinal("prenom")) ? "" : reader.GetString("prenom"),
                                Email      = reader.GetString("email"),
                                MotDePasse = reader.GetString("mot_de_passe"),
                                Role       = reader.GetString("role")
                            };
                        }
                    }
                }
            }
            return null;
        }

        /// <summary>
        /// Met à jour les informations d'un utilisateur (nom, prénom, email, compétences, bio, rôle).
        /// Le mot de passe N'est PAS modifié ici (méthode séparée pour ça).
        /// Retourne true si au moins une ligne a été modifiée.
        /// </summary>
        public bool Update(User user)
        {
            using (var conn = Database.GetConnection())
            {
                if (conn == null) return false;

                string sql = @"UPDATE users
                               SET nom         = @nom,
                                   prenom      = @prenom,
                                   email       = @email,
                                   competences = @competences,
                                   bio         = @bio,
                                   role        = @role
                               WHERE id = @id";

                using (var cmd = new MySqlCommand(sql, conn))
                {
                    cmd.Parameters.AddWithValue("@nom",         user.Nom);
                    cmd.Parameters.AddWithValue("@prenom",      user.Prenom);
                    cmd.Parameters.AddWithValue("@email",       user.Email);
                    cmd.Parameters.AddWithValue("@competences", user.Competences);
                    cmd.Parameters.AddWithValue("@bio",         user.Bio);
                    cmd.Parameters.AddWithValue("@role",        user.Role);
                    cmd.Parameters.AddWithValue("@id",          user.Id);

                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        /// <summary>
        /// Supprime un utilisateur par son id.
        /// Les messages et matches associés sont supprimés automatiquement
        /// grâce à ON DELETE CASCADE défini dans la base PHP.
        /// </summary>
        public bool Delete(int id)
        {
            using (var conn = Database.GetConnection())
            {
                if (conn == null) return false;

                string sql = "DELETE FROM users WHERE id = @id";

                using (var cmd = new MySqlCommand(sql, conn))
                {
                    cmd.Parameters.AddWithValue("@id", id);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }

        /// <summary>
        /// Inverse l'état de bannissement (0→1 ou 1→0).
        /// Retourne le NOUVEL état : true = banni, false = actif.
        /// </summary>
        public bool ToggleBan(int id, bool estActuellementBanni)
        {
            // Si banni → on débannit (0), si actif → on bannit (1)
            int nouvelEtat = estActuellementBanni ? 0 : 1;

            using (var conn = Database.GetConnection())
            {
                if (conn == null) return estActuellementBanni; // Pas de changement si erreur

                string sql = "UPDATE users SET banni = @banni WHERE id = @id";

                using (var cmd = new MySqlCommand(sql, conn))
                {
                    cmd.Parameters.AddWithValue("@banni", nouvelEtat);
                    cmd.Parameters.AddWithValue("@id",    id);
                    cmd.ExecuteNonQuery();
                }
            }

            return nouvelEtat == 1;
        }

        /// <summary>
        /// Met à jour l'email et le mot de passe (hash bcrypt) d'un utilisateur.
        /// Utilisé pour modifier le profil de l'admin connecté.
        /// Le hash doit être généré PAR LE CONTROLLER avant d'appeler cette méthode.
        /// </summary>
        public bool UpdateProfil(int id, string email, string motDePasseHash)
        {
            using (var conn = Database.GetConnection())
            {
                if (conn == null) return false;

                string sql = "UPDATE users SET email = @email, mot_de_passe = @mdp WHERE id = @id";

                using (var cmd = new MySqlCommand(sql, conn))
                {
                    cmd.Parameters.AddWithValue("@email", email);
                    cmd.Parameters.AddWithValue("@mdp",   motDePasseHash);
                    cmd.Parameters.AddWithValue("@id",    id);
                    return cmd.ExecuteNonQuery() > 0;
                }
            }
        }
    }
}
