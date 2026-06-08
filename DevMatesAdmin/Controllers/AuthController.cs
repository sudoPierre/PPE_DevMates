// =============================================
// FICHIER : Controllers/AuthController.cs
// RÔLE    : Gère la connexion de l'administrateur.
//           Vérifie l'email, le mot de passe (bcrypt)
//           et le rôle "admin" dans la base.
//           Conserve l'admin connecté dans AdminConnecte.
// =============================================

using DevMatesAdmin.Models;

namespace DevMatesAdmin.Controllers
{
    public class AuthController
    {
        // Propriété statique : l'admin connecté est accessible depuis toute l'application
        // (ex: MainForm peut afficher son email sans le recalculer)
        public static User AdminConnecte { get; private set; }

        private readonly UserRepository _repo = new UserRepository();

        /// <summary>
        /// Tente de connecter un administrateur avec son email et son mot de passe.
        /// Retourne true si :
        ///   1. L'email existe dans la base
        ///   2. Le rôle est "admin"
        ///   3. Le mot de passe correspond au hash bcrypt stocké
        /// </summary>
        public bool Connexion(string email, string motDePasse)
        {
            // Étape 1 : chercher l'utilisateur par email
            User user = _repo.GetByEmail(email);

            // Étape 2 : vérifier le rôle et le mot de passe
            // BCrypt.Verify compare le mot de passe en clair avec le hash $2y$ de PHP
            if (user != null
                && user.Role == "admin"
                && BCrypt.Net.BCrypt.Verify(motDePasse, user.MotDePasse))
            {
                AdminConnecte = user; // Mémoriser l'admin connecté
                return true;
            }

            return false; // Identifiants incorrects ou pas admin
        }

        /// <summary>
        /// Déconnecte l'administrateur (remet la session à null).
        /// Appelé quand l'admin clique sur "Déconnexion".
        /// </summary>
        public void Deconnexion()
        {
            AdminConnecte = null;
        }
    }
}
