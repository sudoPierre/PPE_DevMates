// =============================================
// FICHIER : Controllers/UserController.cs
// RÔLE    : Fait le lien entre les vues (formulaires)
//           et le UserRepository (accès base de données).
//           Contient la logique métier : hashage du
//           mot de passe, gestion du profil admin, etc.
// =============================================

using System.Collections.Generic;
using DevMatesAdmin.Models;

namespace DevMatesAdmin.Controllers
{
    public class UserController
    {
        private readonly UserRepository _repo = new UserRepository();

        /// <summary>
        /// Retourne la liste complète des utilisateurs.
        /// Appelé par MainForm pour remplir le DataGridView.
        /// </summary>
        public List<User> ObtenirTousLesUtilisateurs()
        {
            return _repo.GetAll();
        }

        /// <summary>
        /// Retourne un utilisateur par son id.
        /// Appelé avant d'ouvrir EditUserForm pour pré-remplir les champs.
        /// </summary>
        public User ObtenirUtilisateur(int id)
        {
            return _repo.GetById(id);
        }

        /// <summary>
        /// Enregistre les modifications d'un utilisateur en base.
        /// Retourne true si la mise à jour a réussi.
        /// </summary>
        public bool ModifierUtilisateur(User user)
        {
            return _repo.Update(user);
        }

        /// <summary>
        /// Supprime un utilisateur de la base par son id.
        /// Retourne true si la suppression a réussi.
        /// </summary>
        public bool SupprimerUtilisateur(int id)
        {
            return _repo.Delete(id);
        }

        /// <summary>
        /// Bascule l'état de bannissement d'un utilisateur (actif ↔ banni).
        /// Retourne le nouvel état : true = maintenant banni.
        /// </summary>
        public bool BasculerBannissement(int id, bool estActuellementBanni)
        {
            return _repo.ToggleBan(id, estActuellementBanni);
        }

        /// <summary>
        /// Met à jour le profil de l'admin connecté (email + éventuellement mot de passe).
        /// Si nouveauMotDePasse est vide, l'ancien hash est conservé (pas de changement).
        /// Si un nouveau mot de passe est fourni, il est hashé en bcrypt avant l'UPDATE.
        /// </summary>
        public bool ModifierProfil(int id, string email, string nouveauMotDePasse)
        {
            string hashAEnregistrer;

            if (string.IsNullOrEmpty(nouveauMotDePasse))
            {
                // Pas de nouveau mot de passe → on récupère le hash existant en base
                User userExistant = _repo.GetById(id);
                hashAEnregistrer = userExistant?.MotDePasse ?? "";
            }
            else
            {
                // Nouveau mot de passe → hashage bcrypt (compatible avec PHP password_hash)
                hashAEnregistrer = BCrypt.Net.BCrypt.HashPassword(nouveauMotDePasse);
            }

            return _repo.UpdateProfil(id, email, hashAEnregistrer);
        }
    }
}
