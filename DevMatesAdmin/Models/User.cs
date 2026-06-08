// =============================================
// FICHIER : Models/User.cs
// RÔLE    : Représente un utilisateur de la table
//           "users" de la base devmates.
//           Chaque propriété = une colonne SQL.
// =============================================

using System;

namespace DevMatesAdmin.Models
{
    /// <summary>
    /// Modèle de données pour un utilisateur DevMates.
    /// Utilisé pour transporter les données entre la base,
    /// les controllers et les formulaires (vues).
    /// </summary>
    public class User
    {
        public int      Id              { get; set; }
        public string   Nom             { get; set; }
        public string   Prenom          { get; set; }
        public string   Email           { get; set; }
        public string   MotDePasse      { get; set; }  // Hash bcrypt stocké en base
        public string   Role            { get; set; }  // "admin" ou "user"
        public string   Competences     { get; set; }
        public string   Bio             { get; set; }
        public DateTime DateInscription { get; set; }
        public bool     Banni           { get; set; }  // false = actif, true = banni
    }
}
